<?php
include 'lang.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?></title>
    <link rel="stylesheet" href="css/public.css">
    <link rel="icon" href="images/logo.png" type="image/png">
</head>
<body>

<header>
    <img src="images/logo.png" alt="Logo">
    <nav class="menu">
        <a href="?lang=pt"><img src="images/brasil-flag.jpg" class="flag-icon" alt="PT"></a>
        <a href="?lang=es"><img src="images/spain-flag.jpg" class="flag-icon" alt="ES"></a>
        <a href="?lang=en"><img src="images/uk-flag.jpg" class="flag-icon" alt="EN"></a>
    </nav>
</header>

<div class="search-bar">
    <form action="mapa.php" method="GET">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        <input type="text" name="q" placeholder="<?= $t['buscar'] ?>..." />
        <button type="submit"><?= $t['buscar'] ?></button>
    </form>
</div>

<div class="container">
    <h2><?= $t['titulo'] ?></h2>
    <p>
        <?= $lang === 'es' ? 'Bienvenido a la plataforma de servicios comunitarios.' :
           ($lang === 'en' ? 'Welcome to the community service platform.' :
           'Bem-vindo à plataforma de serviços comunitários.') ?>
    </p>
    <img src="images/como_funciona.jpg" alt="Como funciona" style="max-width:100%; margin-top:20px;">
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

</body>
</html>
