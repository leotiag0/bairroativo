<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?></title>
    <link rel="stylesheet" href="css/public.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        header {
            flex-shrink: 0;
        }

        .search-bar {
            flex-shrink: 0;
            padding: 10px 20px;
            background: #007bff;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .search-bar input[type="text"] {
            flex: 1 1 60%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
        }

        .search-bar button {
            padding: 10px 20px;
            background: #28a745;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
        }

        .search-bar button:hover {
            background: #218838;
        }

        #map {
            flex: 1;
            min-height: 400px;
        }

        footer {
            flex-shrink: 0;
            background: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
        }
    </style>
</head>
<body>

<header>
    <img src="images/logo.png" alt="Logo">
    <nav class="menu">
        <a href="index.php?lang=<?= $lang ?>">🏠 Início</a>
    </nav>
</header>

<div class="search-bar">
    <form action="mapa.php" method="GET" style="display:flex; flex:1; max-width:900px; gap:10px;">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($filtro) ?>" placeholder="<?= $t['buscar'] ?> por nome, bairro, tipo..." />
        <button type="submit"><?= $t['buscar'] ?></button>
    </form>
</div>

<div id="map"></div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

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
</script>

</body>
</html>
