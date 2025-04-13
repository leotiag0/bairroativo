<?php include 'lang.php'; ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> - Bairro Ativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/public.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/public-dark.css?v=<?= time() ?>">
    <link rel="icon" href="images/logo.png" type="image/png">
    <meta name="robots" content="noindex">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container" style="text-align: center;">
    <h2 style="margin-top: 30px;"><?= $t['titulo'] ?></h2>
    <p style="margin: 10px 0 20px; font-size: 1.1em;"><?= $t['bem_vindo'] ?> ao sistema!</p>

    <div class="index-hero">
        <img src="images/como_funciona.jpg" alt="Como Funciona" class="index-banner">
    </div>

    <div class="index-buttons">
        <a href="mapa.php?lang=<?= $lang ?>" class="btn">🗺️ <?= $t['ver_mapa'] ?? 'Ver Mapa' ?></a>
        <a href="lista.php?lang=<?= $lang ?>" class="btn">📋 <?= $t['ver_lista'] ?? 'Ver Lista' ?></a>
        <a href="admin_login.php" class="btn">🔐 <?= $t['admin'] ?? 'Admin' ?></a>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
