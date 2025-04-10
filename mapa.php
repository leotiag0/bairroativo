<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'lang.php';
require 'conexao.php';

// Filtros únicos
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
$categorias = $pdo->query("SELECT c.id, c.nome FROM categorias c ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Condições dinâmicas
$where = [];
$params = [];
$join_categoria = '';

if (!empty($_GET['q'])) {
    $where[] = "(s.nome_servico LIKE :q OR s.tipo LIKE :q OR s.bairro LIKE :q OR s.cidade LIKE :q)";
    $params[':q'] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['bairro'])) {
    $where[] = "s.bairro = :bairro";
    $params[':bairro'] = $_GET['bairro'];
}
if (!empty($_GET['tipo'])) {
    $where[] = "s.tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}
if (!empty($_GET['categoria'])) {
    $join_categoria = "INNER JOIN servico_categoria sc ON sc.servico_id = s.id";
    $where[] = "sc.categoria_id = :categoria";
    $params[':categoria'] = $_GET['categoria'];
}

// Consulta
$sql = "SELECT s.* FROM servicos s $join_categoria";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON seguro
$json_servicos = json_encode($servicos, JSON_UNESCAPED_UNICODE);
if ($json_servicos === false) {
    $json_servicos = '[]';
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> | Bairro Ativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; color: #333; }

        header {
            background: #007bff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .logo { height: 60px; }
        .flags img { width: 24px; height: 24px; margin-left: 10px; cursor: pointer; }

        .filtros {
            padding: 15px;
            background: #f1f1f1;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .filtros select, .filtros input, .filtros button {
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
        }

        .filtros button {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .filtros button:hover {
            background: #218838;
        }

        #map { height: calc(100vh - 200px); }

        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
        }

        @media (max-width: 768px) {
            .filtros { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

<header>
    <a href="index.php"><img src="images/logo.png" alt="Logo" class="logo"></a>
    <div class="flags">
        <a href="?lang=pt"><img src="images/brasil-flag.jpg" alt="Português"></a>
        <a href="?lang=es"><img src="images/spain-flag.jpg" alt="Español"></a>
    </div>
</header>

<!-- Filtros -->
<div class="filtros">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 10px;">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        <input type="text" name="q" placeholder="<?= $t['buscar'] ?>..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <select name="bairro">
            <option value="">Bairro</option>
            <?php foreach ($bairros as $b): ?>
                <option value="<?= $b ?>" <?= ($_GET['bairro'] ?? '') === $b ? 'selected' : '' ?>><?= $b ?></option>
            <?php endforeach; ?>
        </select>
        <select name="tipo">
            <option value="">Tipo</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t ?>" <?= ($_GET['tipo'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
        <select name="categoria">
            <option value="">Categoria</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($_GET['categoria'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['nome'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">🔍 <?= $t['buscar'] ?></button>
    </form>
    <button onclick="localizarUsuario()">📍 <?= $t['proximo'] ?? 'Perto de mim' ?></button>
</div>

<!-- Mapa -->
<div id="map"></div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

<script>
const map = L.map('map').setView([-23.55, -46.63], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Carregar serviços atuais
const servicos = <?= $json_servicos ?>;

servicos.forEach(s => {
    if (!s.latitude || !s.longitude) return;

    const popup = `
        <strong>${s.nome_servico}</strong><br>
        ${s.endereco}, ${s.bairro}<br>
        <i class="fa fa-layer-group"></i> ${s.tipo}<br>
        <a href="detalhes.php?id=${s.id}&lang=<?= $lang ?>">ℹ️ <?= $t['detalhes'] ?></a>
    `;

    L.marker([s.latitude, s.longitude]).addTo(map).bindPopup(popup);
});

function localizarUsuario() {
    if (!navigator.geolocation) {
        alert("Navegador não suporta geolocalização.");
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
        }).addTo(map).bindPopup("📍 Você está aqui").openPopup();

        map.setView([lat, lng], 14);

        fetch(`ajax/proximos.php?lat=${lat}&lng=${lng}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(s => {
                    if (!s.latitude || !s.longitude) return;

                    const popup = `
                        <strong>${s.nome_servico}</strong><br>
                        ${s.endereco}, ${s.bairro}<br>
                        <i class="fa fa-layer-group"></i> ${s.tipo}<br>
                        <a href="detalhes.php?id=${s.id}&lang=<?= $lang ?>">ℹ️ <?= $t['detalhes'] ?></a>
                    `;

                    L.marker([s.latitude, s.longitude]).addTo(map).bindPopup(popup);
                });

                if (data.length === 0) {
                    alert("Nenhum serviço encontrado em até 10km.");
                }
            })
            .catch(() => alert("Erro ao buscar serviços próximos."));
    }, () => {
        alert("Erro ao obter sua localização.");
    });
}
</script>

</body>
</html>
