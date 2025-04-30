<?php
include 'lang.php';
require 'conexao.php';

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

if (!$s) {
    echo "Serviço não encontrado.";
    exit;
}

$descricao = $lang == 'es' ? $s['descricao_es'] : ($lang == 'en' ? $s['descricao_en'] : $s['descricao_pt']);

$stmtCat = $pdo->prepare("
    SELECT c.nome, c.cor 
    FROM categorias c
    JOIN servico_categoria sc ON sc.categoria_id = c.id
    WHERE sc.servico_id = ?
");
$stmtCat->execute([$id]);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($s['nome_servico']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS principal -->
    <link rel="stylesheet" href="css/public.css?v=<?= filemtime('css/public.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <div class="detalhes-servico">
        <h2><?= htmlspecialchars($s['nome_servico']) ?></h2>

        <p><strong><?= $t['descricao'] ?>:</strong><br><?= nl2br(htmlspecialchars($descricao)) ?></p>
        <p><strong><?= $t['horario'] ?>:</strong> <?= htmlspecialchars($s['horario_inicio']) ?> - <?= htmlspecialchars($s['horario_fim']) ?></p>
        <p><strong><?= $t['local'] ?? 'Local' ?>:</strong> <?= htmlspecialchars($s['endereco']) ?>, <?= htmlspecialchars($s['bairro']) ?>, <?= htmlspecialchars($s['cidade']) ?>/<?= htmlspecialchars($s['estado']) ?></p>

        <?php if (!empty($categorias)): ?>
            <p><strong><?= $t['categorias'] ?? 'Categorias' ?>:</strong><br>
                <?php foreach ($categorias as $cat): ?>
                    <span class="categoria-badge" style="background: <?= htmlspecialchars($cat['cor']) ?>;">
                        <?= htmlspecialchars($cat['nome']) ?>
                    </span>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <div id="map"></div>

        <div class="btn-group">
            <a class="btn" href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($s['endereco'] . ', ' . $s['bairro'] . ', ' . $s['cidade'] . ', ' . $s['estado']) ?>" target="_blank">📍 <?= $t['rota'] ?></a>
            <a class="btn" href="geo:<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">🧭 <?= $t['navegar'] ?></a>
            <button class="btn" onclick="compartilhar()">🔗 <?= $t['compartilhar'] ?></button>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const map = L.map('map').setView([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>]).addTo(map)
            .bindPopup("<?= htmlspecialchars($s['nome_servico']) ?>")
            .openPopup();
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
