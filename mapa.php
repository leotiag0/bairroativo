<?php
include 'lang.php';
require 'conexao.php';

// Consulta de serviços (simples, pode ser filtrado depois)
$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll(PDO::FETCH_ASSOC);
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
        }

        #map {
            flex: 1;
            height: 100%;
            min-height: 500px;
        }

        .filtros {
            padding: 10px;
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
        }

        .btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<header>
    <div><strong><?= $t['titulo'] ?></strong></div>
    <div>
        <button class="btn" onclick="localizarUsuario()">📍 Próximo de mim</button>
    </div>
</header>

<div class="filtros">
    <form method="GET" action="mapa.php">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        <input type="text" name="q" placeholder="<?= $t['buscar'] ?>..." />
        <button type="submit" class="btn"><?= $t['buscar'] ?></button>
    </form>
</div>

<div id="map"></div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

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
