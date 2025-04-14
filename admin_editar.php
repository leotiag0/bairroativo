<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

require 'conexao.php';
include 'admin_header.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE servicos SET 
        nome_servico=?, rua=?, bairro=?, cidade=?, estado=?, tipo=?, descricao=?, 
        horario_inicio=?, horario_fim=?, latitude=?, longitude=?, 
        agendamento_pt=?, agendamento_es=?, agendamento_en=?
        WHERE id=?");

    $stmt->execute([
        $_POST['nome'], $_POST['rua'], $_POST['bairro'], $_POST['cidade'], $_POST['estado'],
        $_POST['tipo'], $_POST['descricao'], $_POST['horario_inicio'], $_POST['horario_fim'],
        $_POST['latitude'], $_POST['longitude'],
        $_POST['agendamento_pt'], $_POST['agendamento_es'], $_POST['agendamento_en'],
        $id
    ]);

    header("Location: admin_gerenciar.php");
    exit;
}
?>

<div class="container">
    <h2>Editar Serviço</h2>
    <form method="POST">
        <input type="text" name="nome" value="<?= $s['nome_servico'] ?>" required>
        <input type="text" name="rua" value="<?= $s['rua'] ?>" required>
        <input type="text" name="bairro" value="<?= $s['bairro'] ?>" required>
        <input type="text" name="cidade" value="<?= $s['cidade'] ?>" required>
        <input type="text" name="estado" value="<?= $s['estado'] ?>" required>
        <input type="text" name="tipo" value="<?= $s['tipo'] ?>" required>
        <textarea name="descricao"><?= $s['descricao'] ?></textarea>
        <input type="time" name="horario_inicio" value="<?= $s['horario_inicio'] ?>" required>
        <input type="time" name="horario_fim" value="<?= $s['horario_fim'] ?>" required>
        <input type="text" name="latitude" value="<?= $s['latitude'] ?>">
        <input type="text" name="longitude" value="<?= $s['longitude'] ?>">
        <textarea name="agendamento_pt"><?= $s['agendamento_pt'] ?></textarea>
        <textarea name="agendamento_es"><?= $s['agendamento_es'] ?></textarea>
        <textarea name="agendamento_en"><?= $s['agendamento_en'] ?></textarea>
        <button type="submit">Salvar Alterações</button>
    </form>
</div>

 <?php include 'admin_footer.php'; ?>
