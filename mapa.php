<?php
include 'lang.php';
require 'conexao.php';

$filtro = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE 
    nome_servico LIKE :filtro OR 
    tipo LIKE :filtro OR 
    bairro LIKE :filtro OR 
    cidade LIKE :filtro");
$stmt->execute([':filtro' => "%$filtro%"]);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?></title>
    <link rel="stylesheet" href="css/public.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        #map { height: 500px; margin-bottom: 20px; }
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
    <form action="mapa.php" method="GET">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($filtro) ?>" placeholder="<?= $t['buscar'] ?> por nome, bairro, tipo..." />
        <button type="submit"><?= $t['buscar'] ?></button>
    </form>
</div>

<div class="container">
    <h2>🗺️ Mapa de Serviços</h2>
    <div id="map"></div>
</div>

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
