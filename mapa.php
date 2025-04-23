<?php
// Configurações de exibição de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclui arquivos necessários
include 'lang.php'; // Arquivo de tradução
require 'conexao.php'; // Conexão com o banco de dados

// Obtém os filtros disponíveis no banco de dados
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
$categorias = $pdo->query("SELECT id, nome, cor FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Mapeia as cores das categorias
$categorias_cores = [];
foreach ($categorias as $cat) {
    $categorias_cores[$cat['id']] = $cat['cor'];
}

// Inicializa os filtros dinâmicos
$where = [];
$params = [];
$join_categoria = '';

// Filtro por busca textual
if (!empty($_GET['q'])) {
    $where[] = "(s.nome_servico LIKE :q OR s.tipo LIKE :q OR s.bairro LIKE :q OR s.cidade LIKE :q)";
    $params[':q'] = '%' . $_GET['q'] . '%';
}

// Filtro por bairro
if (!empty($_GET['bairro'])) {
    $where[] = "s.bairro = :bairro";
    $params[':bairro'] = $_GET['bairro'];
}

// Filtro por tipo de serviço
if (!empty($_GET['tipo'])) {
    $where[] = "s.tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}

// Filtro por categoria
if (!empty($_GET['categoria'])) {
    $where[] = "sc.categoria_id = :categoria";
    $params[':categoria'] = $_GET['categoria'];
}

// Monta a consulta SQL com os filtros aplicados
$sql = "SELECT s.*, sc.categoria_id FROM servicos s
        LEFT JOIN servico_categoria sc ON sc.servico_id = s.id";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " GROUP BY s.id";

// Executa a consulta e obtém os resultados
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Converte os dados para JSON
$json_servicos = json_encode($servicos, JSON_UNESCAPED_UNICODE);
$json_cores = json_encode($categorias_cores);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <!-- Metadados e estilos -->
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> | <?= $t['bairro_ativo'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link rel="stylesheet" href="css/public.css?v=<?= filemtime('css/public.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
<?php include 'header.php'; // Inclui o cabeçalho ?>
<main>
    <!-- Filtros de busca -->
    <div class="filtros">
        <form method="GET" class="filtros-flex">
            <input type="hidden" name="lang" value="<?= $lang ?>">
            <input type="text" name="q" placeholder="<?= $t['buscar'] ?>..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <select name="bairro">
                <option value=""><?= $t['bairro'] ?></option>
                <?php foreach ($bairros as $bairro): ?>
                    <option value="<?= $bairro ?>" <?= ($_GET['bairro'] ?? '') === $bairro ? 'selected' : '' ?>><?= $bairro ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tipo">
                <option value=""><?= $t['tipo'] ?></option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= $tipo ?>" <?= ($_GET['tipo'] ?? '') === $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
                <?php endforeach; ?>
            </select>
            <select name="categoria">
                <option value=""><?= $t['categoria'] ?></option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($_GET['categoria'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['nome'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">🔍 <?= $t['buscar'] ?></button>
            <button type="button" onclick="localizarUsuario()" class="btn btn-localizacao">📍 <?= $t['proximo'] ?? 'Perto de mim' ?></button>
        </form>
    </div>
    <!-- Mapa -->
    <div id="map"></div>
</main>
<?php include 'footer.php'; // Inclui o rodapé ?>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    // Inicializa o mapa e ajusta a altura dinamicamente
    const servicos = <?= $json_servicos ?>;
    const coresPorCategoria = <?= $json_cores ?>;
    let map;
    function ajustarAlturaMapa() {
        const header = document.querySelector('header');
        const filtros = document.querySelector('.filtros');
        const footer = document.querySelector('footer');
        const mapDiv = document.getElementById('map');
        const alturaMapa = window.innerHeight - (header?.offsetHeight || 0) - (filtros?.offsetHeight || 0) - (footer?.offsetHeight || 0);
        mapDiv.style.height = `${alturaMapa}px`;
        if (map) map.invalidateSize();
    }
    window.addEventListener('resize', ajustarAlturaMapa);
    document.addEventListener("DOMContentLoaded", () => {
        ajustarAlturaMapa();
        map = L.map('map').setView([-23.55, -46.63], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        // Adiciona marcadores no mapa
        servicos.forEach(s => {
            if (!s.latitude || !s.longitude) return;
            const cor = coresPorCategoria[s.categoria_id] || '#007bff';
            const marker = L.circleMarker([s.latitude, s.longitude], {
                radius: 8,
                color: cor,
                fillColor: cor,
                fillOpacity: 0.9
            }).addTo(map);
            const popup = `
                <strong>${s.nome_servico}</strong><br>
                ${s.endereco}, ${s.bairro}<br>
                <i class="fa fa-layer-group"></i> ${s.tipo}<br>
                <a href="detalhes.php?id=${s.id}&lang=<?= $lang ?>">ℹ️ <?= htmlspecialchars($t['detalhes']) ?></a>
            `;
            marker.bindPopup(popup);
        });
    });
    // Localiza o usuário no mapa
    function localizarUsuario() {
        if (!navigator.geolocation) {
            alert("<?= $t['erro_geolocalizacao'] ?>");
            return;
        }
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'images/user-location.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                })
            }).addTo(map).bindPopup("📍 <?= $t['voce_esta_aqui'] ?>").openPopup();
            map.setView([lat, lng], 14);
            fetch(`ajax/proximos.php?lat=${lat}&lng=${lng}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(s => {
                        if (!s.latitude || !s.longitude) return;
                        const cor = coresPorCategoria[s.categoria_id] || '#007bff';
                        const marker = L.circleMarker([s.latitude, s.longitude], {
                            radius: 8,
                            color: cor,
                            fillColor: cor,
                            fillOpacity: 0.9
                        }).addTo(map);
                        const popup = `
                            <strong>${s.nome_servico}</strong><br>
                            ${s.endereco}, ${s.bairro}<br>
                            <i class="fa fa-layer-group"></i> ${s.tipo}<br>
                            <a href="detalhes.php?id=${s.id}&lang=<?= $lang ?>">ℹ️ <?= htmlspecialchars($t['detalhes']) ?></a>
                        `;
                        marker.bindPopup(popup);
                    });
                })
                .catch(() => alert("<?= $t['erro_servicos_proximos'] ?>"));
        }, () => {
            alert("<?= $t['erro_obter_localizacao'] ?>");
        });
    }
</script>
</body>
</html>
