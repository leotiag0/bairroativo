<?php
include 'lang.php';
require 'conexao.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

$instrucao = $lang == 'es' ? $s['agendamento_es'] : ($lang == 'en' ? $s['agendamento_en'] : $s['agendamento_pt']);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $s['nome_servico'] ?></title>
    <link rel="stylesheet" href="css/public.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        #map { height: 300px; margin-top: 20px; }
    </style>
</head>
<body>

<header>
    <div>
        <img src="images/logo.png" alt="Logo">
    </div>
    <nav class="menu">
        <a href="mapa.php?lang=<?= $lang ?>">← <?= $t['titulo'] ?></a>
    </nav>
</header>

<div class="container">
    <h2><?= $s['nome_servico'] ?></h2>
    <p><strong><?= $t['descricao'] ?>:</strong> <?= $s['descricao'] ?></p>
    <p><strong><?= $t['horario'] ?>:</strong> <?= $s['horario_inicio'] ?> - <?= $s['horario_fim'] ?></p>
    <p><strong><?= $t['como_agendar'] ?></strong><br><?= nl2br($instrucao) ?></p>

    <div id="map"></div>

    <br>
    <a class="btn" href="https://www.google.com/maps/dir/?destination=<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">📍 <?= $t['rota'] ?></a>
    <a class="btn" href="geo:<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">🧭 <?= $t['navegar'] ?></a>
    <button class="btn" onclick="compartilhar()">🔗 <?= $t['compartilhar'] ?></button>
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo
</footer>

<script>
    const map = L.map('map').setView([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>]).addTo(map).bindPopup("<?= $s['nome_servico'] ?>").openPopup();

    function compartilhar() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                text: "<?= $s['nome_servico'] ?>",
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert("Link copiado!");
        }
    }
</script>

</body>
</html>
