<?php if (!isset($lang)) include 'lang.php'; ?>
<header class="accessible-header" role="banner">
    <div class="header-container">
        <!-- Logo e Navegação Principal -->
        <div class="header-branding">
            <a href="index.php?lang=<?= $lang ?>" class="logo-link">
                <img src="images/logo.png" alt="<?= translate('Mapa de Serviços Públicos de São Paulo', $lang) ?>" class="logo">
            </a>
        </div>

        <!-- Controles de Acessibilidade e Idioma -->
        <div class="header-controls" aria-label="Controles de acessibilidade e idioma">
            <!-- Seletor de Idioma Acessível -->
            <div class="language-selector" role="group" aria-label="Seletor de idioma">
                <button class="lang-btn <?= $lang === 'pt' ? 'active' : '' ?>" data-lang="pt" aria-pressed="<?= $lang === 'pt' ? 'true' : 'false' ?>">
                    <img src="images/brasil-flag.jpg" alt="Português" class="flag-icon">
                    <span class="sr-only">Português</span>
                </button>
                <button class="lang-btn <?= $lang === 'es' ? 'active' : '' ?>" data-lang="es" aria-pressed="<?= $lang === 'es' ? 'true' : 'false' ?>">
                    <img src="images/spain-flag.jpg" alt="Español" class="flag-icon">
                    <span class="sr-only">Español</span>
                </button>
                <button class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>" data-lang="en" aria-pressed="<?= $lang === 'en' ? 'true' : 'false' ?>">
                    <img src="images/uk-flag.jpg" alt="English" class="flag-icon">
                    <span class="sr-only">English</span>
                </button>
            </div>

<<<<<<< HEAD
            <!-- Controles de Acessibilidade -->
            <div class="accessibility-tools" role="group" aria-label="Ferramentas de acessibilidade">
                <button id="toggle-contraste" class="a11y-btn" aria-label="Alternar alto contraste">
                    <i class="fas fa-adjust" aria-hidden="true"></i>
                    <span class="btn-text"><?= translate('Contraste', $lang) ?></span>
                </button>
                
                <button id="toggle-darkmode" class="a11y-btn" aria-label="Alternar modo escuro">
                    <i class="fas fa-moon" aria-hidden="true"></i>
                    <span class="btn-text"><?= translate('Modo Escuro', $lang) ?></span>
                </button>
                
                <button id="font-increase" class="a11y-btn" aria-label="Aumentar tamanho da fonte">
                    <i class="fas fa-text-height" aria-hidden="true"></i>
                </button>
            </div>
        </div>
=======
        <button id="toggle-contraste" title="Ativar/desativar alto contraste">Alto Contraste</button>
        <button type="button" id="toggle-darkmode">Modo Escuro</button>
>>>>>>> parent of 632e031 (Update header.php)
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Controle de Alto Contraste
    const contrastBtn = document.getElementById("toggle-contraste");
    const darkModeBtn = document.getElementById("toggle-darkmode");
    const fontIncreaseBtn = document.getElementById("font-increase");
    const body = document.body;
    const html = document.documentElement;
    
    // Verificar preferências salvas
    if (localStorage.getItem("highContrast") === "true") {
        body.classList.add("high-contrast");
    }
    if (localStorage.getItem("darkMode") === "true") {
        body.classList.add("dark-mode");
    }
    if (localStorage.getItem("fontSize") === "large") {
        html.classList.add("large-font");
    }
    
    // Alternar Alto Contraste
    contrastBtn.addEventListener("click", () => {
        body.classList.toggle("high-contrast");
        localStorage.setItem("highContrast", body.classList.contains("high-contrast"));
    });
    
    // Alternar Modo Escuro
    darkModeBtn.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("darkMode", body.classList.contains("dark-mode"));
    });
    
    // Aumentar Fonte
    fontIncreaseBtn.addEventListener("click", () => {
        html.classList.toggle("large-font");
        localStorage.setItem("fontSize", html.classList.contains("large-font") ? "large" : "normal");
    });
    
    // Troca de Idioma (melhor para acessibilidade que links)
    document.querySelectorAll(".lang-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            const lang = this.getAttribute("data-lang");
            window.location.href = `?lang=${lang}`;
        });
    });
});
</script>
