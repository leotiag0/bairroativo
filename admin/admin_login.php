<?php
session_start();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['usuario'] === 'admin' && $_POST['senha'] === 'senha123') {
        $_SESSION['admin'] = true;
        header('Location: admin_cadastro.php');
        exit;
    } else {
        $msg = 'Credenciais inválidas.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime('css/admin.css') ?>">
</head>
<body class="login-page">
    <form method="POST" class="login-form">
        <h2>Login Admin</h2>
        <?php if ($msg): ?>
            <div class="msg error"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        
        <div class="form-group">
            <input type="text" name="usuario" placeholder="Usuário" required>
        </div>
        
        <div class="form-group">
            <input type="password" name="senha" placeholder="Senha" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</body>
</html>
