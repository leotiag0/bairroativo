<?php include 'lang.php'; ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= ($lang == 'ar' || $lang == 'he') ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($t['titulo']) ?> | Bairro Ativo - <?= htmlspecialchars($t['slogan']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($t['meta_descricao'] ?? 'Encontre serviços públicos e gratuitos na cidade de São Paulo') ?>">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="css/public.css?v=<?= filemtime('css/public.css') ?>">
    <link rel="preload" href="images/como_funciona.jpg" as="image">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body class="home-page">

<?php include 'header.php'; ?>

<main class="container" id="main-content">
    <section class="hero-section" aria-labelledby="main-heading">
        <div class="hero-content">
            <h1 id="main-heading"><?= htmlspecialchars($t['titulo']) ?></h1>
            <p class="lead"><?= htmlspecialchars($t['bem_vindo']) ?> <?= htmlspecialchars($t['descricao_sistema']) ?></p>
            
            <div class="cta-buttons">
                <a href="#features" class="btn btn-secondary"><?= htmlspecialchars($t['saiba_mais']) ?></a>
                <a href="mapa.php?lang=<?= $lang ?>" class="btn btn-primary">
                    <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
                    <?= htmlspecialchars($t['ver_mapa']) ?>
                </a>
            </div>
        </div>
        
        <figure class="hero-image">
            <img src="images/como_funciona.jpg" alt="<?= htmlspecialchars($t['como_funciona_alt']) ?>" 
                 loading="lazy" class="imagem-explicativa">
            <figcaption class="sr-only"><?= htmlspecialchars($t['como_funciona_alt']) ?></figcaption>
        </figure>
    </section>

    <section id="features" class="features-section" aria-labelledby="features-heading">
        <h2 id="features-heading" class="section-title"><?= htmlspecialchars($t['como_utilizar']) ?></h2>
        <p class="section-subtitle"><?= htmlspecialchars($t['escolha_opcao']) ?></p>
        
        <div class="features-grid">
            <article class="feature-card" aria-labelledby="feature-map">
                <div class="card-icon" aria-hidden="true">🗺️</div>
                <h3 id="feature-map"><?= htmlspecialchars($t['ver_mapa']) ?></h3>
                <p><?= htmlspecialchars($t['desc_mapa']) ?></p>
                <a href="mapa.php?lang=<?= $lang ?>" class="btn btn-outline" aria-label="<?= htmlspecialchars($t['acessar_mapa']) ?>">
                    <?= htmlspecialchars($t['acessar']) ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            
            <article class="feature-card" aria-labelledby="feature-list">
                <div class="card-icon" aria-hidden="true">📋</div>
                <h3 id="feature-list"><?= htmlspecialchars($t['ver_lista']) ?></h3>
                <p><?= htmlspecialchars($t['desc_lista']) ?></p>
                <a href="lista.php?lang=<?= $lang ?>" class="btn btn-outline" aria-label="<?= htmlspecialchars($t['acessar_lista']) ?>">
                    <?= htmlspecialchars($t['acessar']) ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            
            <article class="feature-card" aria-labelledby="feature-admin">
                <div class="card-icon" aria-hidden="true">🔐</div>
                <h3 id="feature-admin"><?= htmlspecialchars($t['area_admin']) ?></h3>
                <p><?= htmlspecialchars($t['desc_admin']) ?></p>
                <a href="admin_login.php" class="btn btn-outline" aria-label="<?= htmlspecialchars($t['acessar_admin']) ?>">
                    <?= htmlspecialchars($t['acessar']) ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
        </div>
    </section>

    <section class="localizacao-section" aria-labelledby="localizacao-heading">
        <h2 id="localizacao-heading" class="section-title"><?= htmlspecialchars($t['servicos_proximos']) ?></h2>
        <div class="localizacao-content">
            <p><?= htmlspecialchars($t['desc_servicos_proximos']) ?></p>
            <button id="detect-localizacao" class="btn btn-geolocation">
                <i class="fas fa-location-arrow" aria-hidden="true"></i>
                <?= htmlspecialchars($t['detectar_localizacao']) ?>
            </button>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
// Detecção de geolocalização
document.getElementById('detect-localizacao').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                window.location.href = `mapa.php?lang=<?= $lang ?>&lat=${position.coords.latitude}&lng=${position.coords.longitude}`;
            },
            function(error) {
                alert('<?= htmlspecialchars($t['erro_geolocalizacao']) ?>');
            }
        );
    } else {
        alert('<?= htmlspecialchars($t['geolocalizacao_nao_suportada']) ?>');
    }
});
</script>

</body>
</html>
