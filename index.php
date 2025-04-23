<?php include 'lang.php'; ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> | Bairro Ativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/public.css?v=<?= time() ?>">
    <meta name="robots" content="noindex">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <section class="boas-vindas">
        <h1><?= $t['titulo'] ?></h1>
        <p><?= $t['bem_vindo'] ?></p>
        <img src="images/como_funciona.jpg" alt="<?= $t['como_funciona_alt'] ?>" class="imagem-explicativa">
    </section>

    <section class="acoes">
        <a href="mapa.php?lang=<?= $lang ?>" class="card-acao">
            <div class="icone">🗺️</div>
            <div class="titulo"><?= $t['ver_mapa'] ?></div>
            <div class="desc"><?= $t['ver_mapa_desc'] ?></div>
        </a>
        <a href="lista.php?lang=<?= $lang ?>" class="card-acao">
            <div class="icone">📋</div>
            <div class="titulo"><?= $t['ver_lista'] ?></div>
            <div class="desc"><?= $t['ver_lista_desc'] ?></div>
        </a>
        <a href="admin_login.php" class="card-acao">
            <div class="icone">🔐</div>
            <div class="titulo"><?= $t['admin'] ?></div>
            <div class="desc"><?= $t['admin_desc'] ?></div>
        </a>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
        </a>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>