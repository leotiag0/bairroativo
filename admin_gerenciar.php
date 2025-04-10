<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $pdo->prepare("DELETE FROM servicos WHERE id = ?")->execute([$id]);
    header("Location: admin_gerenciar.php");
    exit;
}

$servicos = $pdo->query("SELECT * FROM servicos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

include 'admin_menu.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Serviços</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #eaeaea; }
        a.botao { padding: 5px 10px; text-decoration: none; color: white; border-radius: 4px; }
        .editar { background: #007bff; }
        .excluir { background: #dc3545; }
    </style>
</head>
<body>

<h2>Serviços Cadastrados</h2>
<p><a href="admin_cadastro.php">+ Novo Serviço</a> | <a href="admin_logout.php">Sair</a></p>

<table>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Bairro</th>
        <th>Tipo</th>
        <th>Ações</th>
    </tr>
    <?php foreach ($servicos as $s): ?>
    <tr>
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['nome_servico']) ?></td>
        <td><?= htmlspecialchars($s['bairro']) ?></td>
        <td><?= htmlspecialchars($s['tipo']) ?></td>
        <td>
            <a class="botao editar" href="admin_editar.php?id=<?= $s['id'] ?>">Editar</a>
            <a class="botao excluir" href="?excluir=<?= $s['id'] ?>" onclick="return confirm('Excluir este serviço?')">Excluir</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
