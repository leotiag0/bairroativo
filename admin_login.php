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
    <title>Login Admin</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        input { display: block; width: 100%; padding: 10px; margin: 10px 0; }
        button { background: #007bff; color: white; padding: 10px; border: none; width: 100%; }
        .msg { color: red; text-align: center; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Login Admin</h2>
        <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
        <input type="text" name="usuario" placeholder="Usuário">
        <input type="password" name="senha" placeholder="Senha">
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
