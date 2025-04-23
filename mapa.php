<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> | <?= $t['bairro_ativo'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link rel="stylesheet" href="css/public.css?v=<?= filemtime('css/public.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        #toggle-darkmode {
            background: #444;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            margin-left: 10px;
        }
        body.modo-escuro {
            background-color: #1c1c1c;
            color: #f1f1f1;
        }
        body.modo-escuro header,
        body.modo-escuro footer {
            background-color: #2a2a2a !important;
        }
        body.modo-escuro .filtros,
        body.modo-escuro .card,
        body.modo-escuro .container {
            background-color: #2c2c2c;
            color: #f1f1f1;
        }
        body.modo-escuro a {
            color: #89c9ff;
        }
        body.modo-escuro .btn {
            background-color: #555;
            color: #fff;
        }
        body.modo-escuro .btn:hover {
            background-color: #666;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<main>
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
            <button type="button" id="toggle-darkmode">🌓 <?= $t['modo_escuro'] ?></button>
        </form>
    </div>
    <div id="map"></div>
</main>
<?php include 'footer.php'; ?>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
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
    document.getElementById('toggle-darkmode').addEventListener('click', () => {
        document.body.classList.toggle('modo-escuro');
        const darkModeActive = document.body.classList.contains('modo-escuro');
        localStorage.setItem('modo-escuro', darkModeActive);
    });
    if (localStorage.getItem('modo-escuro') === 'true') {
        document.body.classList.add('modo-escuro');
    }
});
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