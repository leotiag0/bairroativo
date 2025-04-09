<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Bairro Ativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">

    <style>
        /* ... mesmo CSS que você usou, já inserido ... */
        body { font-family: Arial; background-color: #f4f4f4; color: #333; }
        header { background-color: #007bff; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .logo { height: 80px; }
        .menu { display: flex; gap: 10px; }
        .flag-icon { width: 28px; height: 28px; cursor: pointer; border-radius: 50%; }
        .intro { padding: 40px 20px; text-align: center; }
        .intro img { max-width: 90%; border-radius: 10px; margin-top: 20px; }
        .action-buttons { display: flex; justify-content: center; gap: 20px; margin-top: 30px; flex-wrap: wrap; }
        .action-buttons a { background: #28a745; color: white; text-decoration: none; padding: 12px 20px; border-radius: 5px; transition: 0.3s; }
        .action-buttons a:hover { background-color: #218838; }
        footer { background: #007bff; color: white; padding: 20px; text-align: center; margin-top: 60px; }
        @media (max-width: 768px) { .action-buttons { flex-direction: column; } }
    </style>
</head>
<body>

<header>
    <img src="images/logo.png" alt="Logo" class="logo">
    <div class="menu">
        <img src="images/brasil-flag.jpg" class="flag-icon" title="Português">
        <img src="images/spain-flag.jpg" class="flag-icon" title="Español">
    </div>
</header>

<section class="intro">
    <h1>Mapeamento de Serviços Públicos e Privados</h1>
    <p>Encontre facilmente os serviços disponíveis na sua cidade.</p>
    <img src="images/como_funciona.jpg" alt="Como funciona?">
    <div class="action-buttons">
        <a href="mapa.php"><i class="fas fa-map-marked-alt"></i> Ver Mapa</a>
        <a href="admin_login.php"><i class="fas fa-user-shield"></i> Acesso Admin</a>
    </div>
</section>

<footer>
    &copy; <?php echo date('Y'); ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

</body>
</html>
