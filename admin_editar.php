<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

require 'conexao.php';

$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE servicos SET nome_servico=?, endereco=?, bairro=?, tipo=?, descricao=?, horario_inicio=?, horario_fim=? WHERE id=?");
    $stmt->execute([
        $_POST['nome'], $_POST['endereco'], $_POST['bairro'], $_POST['tipo'],
        $_POST['descricao'], $_POST['horario_inicio'], $_POST['horario_fim'], $id
    ]);
    header("Location: admin_gerenciar.php");
    exit;
}

include 'admin_menu.php';

$dados = $pdo->prepare("SELECT * FROM servicos WHERE id=?");
$dados->execute([$id]);
$s = $dados->fetch();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Serviço</title>
</head>
<body>
    <h2>Editar Serviço</h2>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= $s['nome_servico'] ?>"><br><br>
        <label>Endereço:</label><br>
        <input type="text" name="endereco" value="<?= $s['endereco'] ?>"><br><br>
        <label>Bairro:</label><br>
        <input type="text" name="bairro" value="<?= $s['bairro'] ?>"><br><br>
        <label>Tipo:</label><br>
        <input type="text" name="tipo" value="<?= $s['tipo'] ?>"><br><br>
        <label>Descrição:</label><br>
        <textarea name="descricao"><?= $s['descricao'] ?></textarea><br><br>
        <label>Horário:</label><br>
        <input type="time" name="horario_inicio" value="<?= $s['horario_inicio'] ?>">
        até
        <input type="time" name="horario_fim" value="<?= $s['horario_fim'] ?>"><br><br>
        <button type="submit">Salvar</button>
        <a href="admin_gerenciar.php">Cancelar</a>
    </form>
</body>
</html>
