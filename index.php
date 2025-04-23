<?php 
// Inclui o arquivo de idioma
include 'lang.php'; 
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <!-- Define o título da página com base no idioma -->
    <title><?= $t['titulo'] ?> | Bairro Ativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Inclui o arquivo CSS com um parâmetro para evitar cache -->
    <link rel="stylesheet" href="css/public.css?v=<?= time() ?>">
    <!-- Define que a página não deve ser indexada pelos motores de busca -->
    <meta name="robots" content="noindex">
</head>
<body>

<?php 
// Inclui o cabeçalho da página
include 'header.php'; 
?>

<main class="container">
    <section class="boas-vindas">
        <!-- Exibe o título e a mensagem de boas-vindas com base no idioma -->
        <h1><?= $t['titulo'] ?></h1>
        <p><?= $t['bem_vindo'] ?></p>
        <!-- Exibe uma imagem explicativa com texto alternativo baseado no idioma -->
        <img src="images/como_funciona.jpg" alt="<?= $t['como_funciona_alt'] ?>" class="imagem-explicativa">
    </section>

    <section class="acoes">
        <!-- Link para o mapa com o idioma como parâmetro -->
        <a href="mapa.php?lang=<?= $lang ?>" class="card-acao">
            <div class="icone">🗺️</div>
            <div class="titulo"><?= $t['ver_mapa'] ?></div>
            <div class="desc"><?= $t['ver_mapa_desc'] ?></div>
        </a>
        <!-- Link para a lista com o idioma como parâmetro -->
        <a href="lista.php?lang=<?= $lang ?>" class="card-acao">
            <div class="icone">📋</div>
            <div class="titulo"><?= $t['ver_lista'] ?></div>
            <div class="desc"><?= $t['ver_lista_desc'] ?></div>
        </a>
        <!-- Link para a área de login do administrador -->
        <a href="admin_login.php" class="card-acao">
            <div class="icone">🔐</div>
            <div class="titulo"><?= $t['admin'] ?></div>
            <div class="desc"><?= $t['admin_desc'] ?></div>
        </a>
    </section>
</main>

<?php 
// Inclui o rodapé da página
include 'footer.php'; 
?>

</body>
</html>