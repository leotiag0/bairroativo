<?php
require 'conexao.php';

$id = $_GET['id'] ?? null;
$lang = $_GET['lang'] ?? 'pt';

$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

$instrucao = $lang == 'es' ? $s['agendamento_es'] : ($lang == 'en' ? $s['agendamento_en'] : $s['agendamento_pt']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $s['nome_servico'] ?> - Detalhes</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        body { font-family: Arial; margin: 0; }
        header, footer { background: #007bff; color: white; padding: 10px 20px; text-align: center; }
        .container { padding: 20px; max-width: 800px; margin: auto; }
        #map { height: 300px; margin-top: 20px; }
        button, a.btn {
            display: inline-block; padding: 10px 15px; margin: 10px 5px;
            background: #28a745; color: white; text-decoration: none; border-radius: 5px;
        }
        button:hover, a.btn:hover { background: #218838; }
        .agendamento { margin-top: 20px; background: #f9f9f9; padding: 15px; border-radius: 6px; }
    </style>
</head>
<body>

<header>
    <h2><?= $s['nome_servico'] ?></h2>
</header>

<div class="container">
    <p><strong>Endereço:</strong> <?= "{$s['rua']}, {$s['bairro']}, {$s['cidade']} - {$s['estado']}" ?></p>
    <p><strong>Horário:</strong> <?= $s['horario_inicio'] ?> às <?= $s['horario_fim'] ?></p>
    <p><strong>Descrição:</strong> <?= nl2br($s['descricao']) ?></p>

    <div id="map"></div>

    <div class="agendamento">
        <strong><?= $lang === 'es' ? 'Cómo agendar:' : ($lang === 'en' ? 'How to book:' : 'Como agendar:') ?></strong><br>
        <?= nl2br($instrucao) ?>
    </div>

    <br>
    <a class="btn" href="https://www.google.com/maps/dir/?api=1&destination=<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">📍 Traçar Rota</a>
    <a class="btn" href="geo:<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">🧭 Navegar</a>
    <button onclick="compartilhar()">🔗 Compartilhar</button>
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

<script>
    const map = L.map('map').setView([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>]).addTo(map).bindPopup("<?= $s['nome_servico'] ?>").openPopup();

    function compartilhar() {
        if (navigator.share) {
            navigator.share({
                title: "<?= $s['nome_servico'] ?>",
                text: "Veja esse serviço no mapa:",
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert("Link copiado para a área de transferência.");
        }
    }
</script>

</body>
</html>
