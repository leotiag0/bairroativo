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
    <title><?= htmlspecialchars($s['nome_servico']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS principal -->
    <link rel="stylesheet" href="css/public.css">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <style>
        #map {
            height: 300px;
            width: 100%;
            margin-top: 20px;
            border-radius: 6px;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<header>
    <a href="index.php?lang=<?= $lang ?>"><img src="images/logo.png" alt="Logo" class="logo"></a>
    <nav class="menu">
        <a href="mapa.php?lang=<?= $lang ?>">← <?= htmlspecialchars($t['titulo']) ?></a>
    </nav>
</header>

<main class="container">
    <h2><?= htmlspecialchars($s['nome_servico']) ?></h2>

    <p><strong><?= $t['descricao'] ?>:</strong> <?= nl2br(htmlspecialchars($s['descricao'])) ?></p>
    <p><strong><?= $t['horario'] ?>:</strong> <?= htmlspecialchars($s['horario_inicio']) ?> - <?= htmlspecialchars($s['horario_fim']) ?></p>
    <p><strong><?= $t['como_agendar'] ?></strong><br><?= nl2br(htmlspecialchars($instrucao)) ?></p>

    <div id="map"></div>

    <div class="btn-group">
        <a class="btn" href="https://www.google.com/maps/dir/?destination=<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">📍 <?= $t['rota'] ?></a>
        <a class="btn" href="geo:<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">🧭 <?= $t['navegar'] ?></a>
        <button class="btn" onclick="compartilhar()">🔗 <?= $t['compartilhar'] ?></button>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo
</footer>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const map = L.map('map').setView([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>]).addTo(map).bindPopup("<?= htmlspecialchars($s['nome_servico']) ?>").openPopup();
    });

    function compartilhar() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                text: "<?= htmlspecialchars($s['nome_servico']) ?>",
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
