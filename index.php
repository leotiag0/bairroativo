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
        <p><?= $t['bem_vindo'] ?> ao sistema! Encontre serviços públicos e gratuitos próximos de você.</p>
        <img src="images/como_funciona.jpg" alt="Como Funciona" class="imagem-explicativa">
    </section>

    <section class="acoes">
        <a href="mapa.php?lang=<?= $lang ?>" class="card-acao">
            <div class="icone">🗺️</div>
            <div class="titulo">Ver Mapa</div>
            <div class="desc">Explore serviços geolocalizados no mapa interativo.</div>
        </a>
        <a href="lista.php?lang=<?= $lang ?>" class="card-acao">
            <div class="icone">📋</div>
            <div class="titulo">Ver Lista</div>
            <div class="desc">Veja todos os serviços em formato de lista filtrável.</div>
        </a>
        <a href="admin_login.php" class="card-acao">
            <div class="icone">🔐</div>
            <div class="titulo">Admin</div>
            <div class="desc">Acesse a área administrativa do sistema.</div>
        </a>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
