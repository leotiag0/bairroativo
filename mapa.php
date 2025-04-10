<?php
include 'lang.php';
require 'conexao.php';

// Preparar listas para os filtros
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos   = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);

// Filtros da URL
$where = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = "(nome_servico LIKE :q OR descricao LIKE :q)";
    $params[':q'] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['bairro'])) {
    $where[] = "bairro = :bairro";
    $params[':bairro'] = $_GET['bairro'];
}
if (!empty($_GET['tipo'])) {
    $where[] = "tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}

$sql = "SELECT * FROM servicos";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        html, body {
            margin: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
        }

        header {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .filtros {
            background: #f2f2f2;
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .filtros select,
        .filtros input[type="text"],
        .filtros button {
            padding: 8px 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn-green {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-green:hover {
            background: #218838;
        }

        #map {
            flex: 1;
            height: 100%;
            min-height: 500px;
        }

        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>

<header>
    <div><strong><?= $t['titulo'] ?></strong></div>
    <button class="btn-green" onclick="localizarUsuario()">📍 <?= $t['navegar'] ?></button>
</header>

<!-- FILTROS -->
<div class="filtros">
    <form method="GET" action="mapa.php" style="display:flex; flex-wrap:wrap; gap:10px;">
        <input type="hidden" name="lang" value="<?= $lang ?>">

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

        <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="<?= $t['buscar'] ?>..." />

        <button type="submit" class="btn-green"><?= $t['buscar'] ?></button>
    </form>
</div>

<!-- MAPA -->
<div id="map"></div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

<!-- Leaflet e Mapa -->
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-23.55, -46.63], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const lang = '<?= $lang ?>';
    const servicos = <?= json_encode($servicos) ?>;

    servicos.forEach(s => {
        if (!s.latitude || !s.longitude) return;

        const popup = `
            <strong>${s.nome_servico}</strong><br>
            ${s.rua}, ${s.bairro}, ${s.cidade}<br>
            <a href="detalhes.php?id=${s.id}&lang=${lang}">ℹ️ <?= $t['detalhes'] ?></a>
        `;

        L.marker([s.latitude, s.longitude]).addTo(map).bindPopup(popup);
    });

    function localizarUsuario() {
        if (!navigator.geolocation) {
            alert("Navegador não suporta geolocalização.");
            return;
        }

        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/64/64113.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                })
            }).addTo(map).bindPopup("📍 Você está aqui").openPopup();

            map.setView([lat, lng], 15);
        }, function() {
            alert("Não foi possível obter sua localização.");
        });
    }
</script>

</body>
</html>
