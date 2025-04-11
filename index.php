<?php include 'lang.php'; ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?></title>
    <link rel="stylesheet" href="css/public.css">
</head>
<body>

<header>
    <div>
        <img src="images/logo.png" alt="Logo">
    </div>
    <nav class="menu">
        <a href="?lang=pt"><img src="images/brasil-flag.jpg" class="flag-icon"></a>
        <a href="?lang=es"><img src="images/spain-flag.jpg" class="flag-icon"></a>
        <a href="?lang=en"><img src="images/uk-flag.jpg" class="flag-icon"></a>
    </nav>
</header>

    <button id="toggle-contraste">♿ Contraste</button>
<script>
document.getElementById('toggle-contraste').onclick = () => {
    document.body.classList.toggle('contraste-alto');
    localStorage.setItem('contrasteAtivo', document.body.classList.contains('contraste-alto'));
};
if (localStorage.getItem('contrasteAtivo') === 'true') {
    document.body.classList.add('contraste-alto');
}
</script>


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

<footer>
    <?php include 'footer.php'; ?>
</footer>
</body>
</html>
