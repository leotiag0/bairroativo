<?php 
// Inicialização segura
require_once 'lang.php';

// Verificação de arquivos incluídos
$header_file = __DIR__ . '/header.php';
$footer_file = __DIR__ . '/footer.php';

if (!file_exists($header_file) || !file_exists($footer_file)) {
    die('Erro: Arquivos essenciais não encontrados');
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= in_array($lang, ['ar', 'he']) ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($t['titulo'] ?? 'Bairro Ativo') ?> | <?= htmlspecialchars($t['slogan'] ?? 'Serviços Públicos') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($t['meta_descricao'] ?? 'Encontre serviços públicos e gratuitos na cidade de São Paulo') ?>">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Pré-carregamento otimizado -->
    <link rel="preload" href="css/public.css" as="style">
    <link rel="preload" href="images/como_funciona.jpg" as="image">
    
    <!-- Favicon com fallback -->
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    
    <!-- CSS principal com fallback -->
    <link rel="stylesheet" href="css/public.css?v=<?= filemtime('css/public.css') ?>">
    <noscript>
        <link rel="stylesheet" href="css/noscript.css">
    </noscript>
    
    <!-- Font Awesome com fallback local -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
    if (!document.fonts.check('1em FontAwesome')) {
        document.write('<link rel="stylesheet" href="css/fontawesome-all.min.css">');
    }
    </script>
</head>
<body class="home-page">

<?php include $header_file; ?>

<main class="container" id="main-content">
    <section class="hero-section" aria-labelledby="main-heading">
        <div class="hero-content">
            <h1 id="main-heading"><?= htmlspecialchars($t['titulo'] ?? 'Bairro Ativo') ?></h1>
            <p class="lead">
                <?= htmlspecialchars($t['bem_vindo'] ?? 'Bem-vindo') ?>
                <?= htmlspecialchars($t['descricao_sistema'] ?? 'ao sistema de serviços públicos') ?>
            </p>
            
            <div class="cta-buttons">
                <a href="#features" class="btn btn-secondary">
                    <?= htmlspecialchars($t['saiba_mais'] ?? 'Saiba mais') ?>
                </a>
                <a href="mapa.php?lang=<?= urlencode($lang) ?>" class="btn btn-primary">
                    <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
                    <?= htmlspecialchars($t['ver_mapa'] ?? 'Ver Mapa') ?>
                </a>
            </div>
        </div>
        
        <figure class="hero-image">
            <img src="images/como_funciona.jpg" 
                 alt="<?= htmlspecialchars($t['como_funciona_alt'] ?? 'Ilustração mostrando como usar o sistema') ?>" 
                 loading="lazy" 
                 class="imagem-explicativa"
                 width="800" 
                 height="450">
            <figcaption class="sr-only">
                <?= htmlspecialchars($t['como_funciona_alt'] ?? 'Ilustração mostrando como usar o sistema') ?>
            </figcaption>
        </figure>
    </section>

    <section id="features" class="features-section" aria-labelledby="features-heading">
        <h2 id="features-heading" class="section-title">
            <?= htmlspecialchars($t['como_utilizar'] ?? 'Como utilizar o sistema') ?>
        </h2>
        <p class="section-subtitle">
            <?= htmlspecialchars($t['escolha_opcao'] ?? 'Escolha a opção que melhor atende suas necessidades') ?>
        </p>
        
        <div class="features-grid">
            <!-- Card do Mapa -->
            <article class="feature-card" aria-labelledby="feature-map">
                <div class="card-icon" aria-hidden="true">🗺️</div>
                <h3 id="feature-map"><?= htmlspecialchars($t['ver_mapa'] ?? 'Ver Mapa') ?></h3>
                <p><?= htmlspecialchars($t['desc_mapa'] ?? 'Explore serviços no mapa interativo') ?></p>
                <a href="mapa.php?lang=<?= urlencode($lang) ?>" class="btn btn-outline" 
                   aria-label="<?= htmlspecialchars($t['acessar_mapa'] ?? 'Acessar mapa de serviços') ?>">
                    <?= htmlspecialchars($t['acessar'] ?? 'Acessar') ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            
            <!-- Card da Lista -->
            <article class="feature-card" aria-labelledby="feature-list">
                <div class="card-icon" aria-hidden="true">📋</div>
                <h3 id="feature-list"><?= htmlspecialchars($t['ver_lista'] ?? 'Ver Lista') ?></h3>
                <p><?= htmlspecialchars($t['desc_lista'] ?? 'Veja todos os serviços em formato de lista') ?></p>
                <a href="lista.php?lang=<?= urlencode($lang) ?>" class="btn btn-outline" 
                   aria-label="<?= htmlspecialchars($t['acessar_lista'] ?? 'Acessar lista de serviços') ?>">
                    <?= htmlspecialchars($t['acessar'] ?? 'Acessar') ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            
            <!-- Card de Admin -->
            <article class="feature-card" aria-labelledby="feature-admin">
                <div class="card-icon" aria-hidden="true">🔐</div>
                <h3 id="feature-admin"><?= htmlspecialchars($t['area_admin'] ?? 'Área Administrativa') ?></h3>
                <p><?= htmlspecialchars($t['desc_admin'] ?? 'Acesse o painel de administração') ?></p>
                <a href="admin_login.php" class="btn btn-outline" 
                   aria-label="<?= htmlspecialchars($t['acessar_admin'] ?? 'Acessar área administrativa') ?>">
                    <?= htmlspecialchars($t['acessar'] ?? 'Acessar') ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
        </div>
    </section>

    <section class="localizacao-section" aria-labelledby="localizacao-heading">
        <h2 id="localizacao-heading" class="section-title">
            <?= htmlspecialchars($t['servicos_proximos'] ?? 'Serviços próximos a você') ?>
        </h2>
        <div class="localizacao-content">
            <p><?= htmlspecialchars($t['desc_servicos_proximos'] ?? 'Encontre serviços próximos à sua localização atual') ?></p>
            <button id="detect-localizacao" class="btn btn-geolocation">
                <i class="fas fa-location-arrow" aria-hidden="true"></i>
                <?= htmlspecialchars($t['detectar_localizacao'] ?? 'Detectar minha localização') ?>
            </button>
            <p id="geo-status" class="sr-only" aria-live="polite"></p>
        </div>
    </section>
</main>

<?php include $footer_file; ?>

<script>
// Detecção de geolocalização aprimorada
document.addEventListener('DOMContentLoaded', function() {
    const geoButton = document.getElementById('detect-localizacao');
    const geoStatus = document.getElementById('geo-status');
    
    if (geoButton) {
        geoButton.addEventListener('click', function() {
            if (!navigator.geolocation) {
                geoStatus.textContent = '<?= htmlspecialchars($t['geolocalizacao_nao_suportada'] ?? 'Geolocalização não suportada em seu navegador') ?>';
                alert(geoStatus.textContent);
                return;
            }
            
            geoStatus.textContent = '<?= htmlspecialchars($t['detectando_localizacao'] ?? 'Detectando sua localização...') ?>';
            geoButton.disabled = true;
            geoButton.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> <?= htmlspecialchars($t['processando'] ?? 'Processando...') ?>';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    window.location.href = `mapa.php?lang=<?= urlencode($lang) ?>&lat=${position.coords.latitude}&lng=${position.coords.longitude}`;
                },
                function(error) {
                    let errorMessage = '<?= htmlspecialchars($t['erro_geolocalizacao'] ?? 'Erro ao obter localização') ?>';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = '<?= htmlspecialchars($t['permissao_negada'] ?? 'Permissão para localização foi negada') ?>';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = '<?= htmlspecialchars($t['localizacao_indisponivel'] ?? 'Informações de localização indisponíveis') ?>';
                            break;
                        case error.TIMEOUT:
                            errorMessage = '<?= htmlspecialchars($t['tempo_esgotado'] ?? 'Tempo de espera para localização esgotado') ?>';
                            break;
                    }
                    
                    geoStatus.textContent = errorMessage;
                    alert(errorMessage);
                    geoButton.disabled = false;
                    geoButton.innerHTML = '<i class="fas fa-location-arrow" aria-hidden="true"></i> <?= htmlspecialchars($t['tentar_novamente'] ?? 'Tentar novamente') ?>';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
});
</script>
</body>
</html>
