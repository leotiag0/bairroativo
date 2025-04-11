<?php include 'lang.php'; ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?></title>
    <link rel="stylesheet" href="css/public.css">
    <meta name="robots" content="noindex">
</head>
<body>

    <?php include 'header.php'; ?>


<div class="container">
    <h2><?= $t['titulo'] ?></h2>
    <p><?= $t['bem_vindo'] ?> ao sistema!</p>
    <img src="images/como_funciona.jpg" style="max-width:100%; margin:20px 0;">
    <div style="margin-top: 20px;">
    <a href="mapa.php?lang=<?= $lang ?>" class="btn">🗺️ Ver Mapa</a>
    <a href="lista.php?lang=<?= $lang ?>" class="btn">📋 Ver Lista</a>
    <a href="admin_login.php" class="btn">🔐 Admin</a>
</div>

</div>

    <?php include 'footer.php'; ?>

</body>
</html>
