<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

// Totais
$total = $pdo->query("SELECT COUNT(*) FROM servicos")->fetchColumn();
$porCategoria = $pdo->query("
    SELECT c.nome, COUNT(*) as total
    FROM categorias c
    JOIN servico_categoria sc ON sc.categoria_id = c.id
    GROUP BY c.id
")->fetchAll(PDO::FETCH_ASSOC);

$porTipo = $pdo->query("
    SELECT tipo, COUNT(*) as total
    FROM servicos GROUP BY tipo
")->fetchAll(PDO::FETCH_ASSOC);

$porBairro = $pdo->query("
    SELECT bairro, COUNT(*) as total
    FROM servicos GROUP BY bairro ORDER BY total DESC LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Estatísticas - Bairro Ativo</title>
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas {
            max-width: 100%;
            height: auto;
            margin: 20px 0;
        }
    </style>
</head>
    
<body>

<?php include 'admin_header.php'?>

<div class="container">
    <h2>📊 Estatísticas do Sistema</h2>

    <p><strong>Total de serviços cadastrados:</strong> <?= $total ?></p>

    <h3>Por Categoria</h3>
    <canvas id="graficoCategorias"></canvas>

    <h3>Por Tipo</h3>
    <ul>
        <?php foreach ($porTipo as $linha): ?>
            <li><?= htmlspecialchars($linha['tipo']) ?>: <?= $linha['total'] ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Bairros com mais serviços</h3>
    <ul>
        <?php foreach ($porBairro as $linha): ?>
            <li><?= htmlspecialchars($linha['bairro']) ?>: <?= $linha['total'] ?></li>
        <?php endforeach; ?>
    </ul>

    <p><a href="admin_gerenciar.php" class="btn">← Voltar</a></p>
</div>

<script>
const ctx = document.getElementById('graficoCategorias');
const grafico = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($porCategoria, 'nome')) ?>,
        datasets: [{
            label: 'Serviços por Categoria',
            data: <?= json_encode(array_column($porCategoria, 'total')) ?>,
            backgroundColor: [
                '#28a745', '#007bff', '#ffc107', '#6610f2', '#fd7e14',
                '#20c997', '#dc3545', '#6f42c1', '#795548', '#17a2b8',
                '#6c757d', '#e83e8c', '#17c671', '#ff5733'
            ]
        }]
    }
});
</script>
<?php include 'admin_footer.php'?>
</body>
</html>
