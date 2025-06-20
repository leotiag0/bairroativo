<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

require '../conexao.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação simples
    $nome_servico = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
    $endereco = filter_var($_POST['endereco'], FILTER_SANITIZE_STRING);
    $bairro = filter_var($_POST['bairro'], FILTER_SANITIZE_STRING);
    $cidade = filter_var($_POST['cidade'], FILTER_SANITIZE_STRING);
    $estado = filter_var($_POST['estado'], FILTER_SANITIZE_STRING);
    $tipo = filter_var($_POST['tipo'], FILTER_SANITIZE_STRING);
    $descricao = filter_var($_POST['descricao'], FILTER_SANITIZE_STRING);
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];
    $latitude = filter_var($_POST['latitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_var($_POST['longitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $descricao_pt = filter_var($_POST['descricao_pt'], FILTER_SANITIZE_STRING);
    $descricao_es = filter_var($_POST['descricao_es'], FILTER_SANITIZE_STRING);
    $descricao_en = filter_var($_POST['descricao_en'], FILTER_SANITIZE_STRING);

    // Atualiza no banco
    $stmt = $pdo->prepare("UPDATE servicos SET 
        nome_servico=?, endereco=?, bairro=?, cidade=?, estado=?, tipo=?, 
        horario_inicio=?, horario_fim=?, latitude=?, longitude=?, 
        descricao_pt=?, descricao_es=?, descricao_en=?
        WHERE id=?");

    $stmt->execute([
        $nome_servico, $endereco, $bairro, $cidade, $estado, 
        $tipo, $horario_inicio, $horario_fim, 
        $latitude, $longitude, $descricao_pt, $descricao_es, $descricao_en, $id
    ]);

    // Mensagem de sucesso
    $msg = 'Serviço atualizado com sucesso!';
    header("Location: admin_gerenciar.php?msg=" . urlencode($msg));
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Serviço</title>
    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime('css/admin.css') ?>">
</head>

<body>
<?php include 'admin_header.php'; ?>
<div class="container">
    <h2>Editar Serviço</h2>
    
    <!-- Mensagem de sucesso -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="nome">Nome do Serviço:</label>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($s['nome_servico']) ?>" required>

        <label for="enderecoa">Endereço:</label>
        <input type="text" name="endereco" id="endereco" value="<?= htmlspecialchars($s['endereco']) ?>" required>

        <label for="bairro">Bairro:</label>
        <input type="text" name="bairro" id="bairro" value="<?= htmlspecialchars($s['bairro']) ?>" required>

        <label for="cidade">Cidade:</label>
        <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($s['cidade']) ?>" required>

        <label for="estado">Estado:</label>
        <input type="text" name="estado" id="estado" value="<?= htmlspecialchars($s['estado']) ?>" required>

        <label for="tipo">Tipo:</label>
        <input type="text" name="tipo" id="tipo" value="<?= htmlspecialchars($s['tipo']) ?>" required>

        <label for="horario_inicio">Horário Início:</label>
        <input type="time" name="horario_inicio" id="horario_inicio" value="<?= htmlspecialchars($s['horario_inicio']) ?>" required>

        <label for="horario_fim">Horário Fim:</label>
        <input type="time" name="horario_fim" id="horario_fim" value="<?= htmlspecialchars($s['horario_fim']) ?>" required>

        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" id="latitude" value="<?= htmlspecialchars($s['latitude']) ?>">

        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" id="longitude" value="<?= htmlspecialchars($s['longitude']) ?>">

        <label for="agendamento_pt">Descrição (PT):</label>
        <textarea name="agendamento_pt" id="descricao_pt"><?= htmlspecialchars($s['descricao_pt']) ?></textarea>

        <label for="agendamento_es">Descrição (ES):</label>
        <textarea name="agendamento_es" id="descricao_es"><?= htmlspecialchars($s['descricao_es']) ?></textarea>

        <label for="agendamento_en">Descrição (EN):</label>
        <textarea name="agendamento_en" id="descricao_en"><?= htmlspecialchars($s['descricao_en']) ?></textarea>

        <button type="submit" class="btn">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>
</body>
</html>
