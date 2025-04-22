<?php
// Verificação robusta de inclusões
$lang_file = __DIR__ . '/lang.php';
if (!isset($lang) && file_exists($lang_file)) {
    include $lang_file;
} elseif (!isset($lang)) {
    $lang = 'pt'; // Idioma padrão
}

// Função de tradução fallback
if (!function_exists('translate')) {
    function translate($text, $lang) {
        return $text; // Retorna o texto original se não houver tradução
    }
}
?>
<header class="accessible-header" role="banner">
    <div class="header-container">
        <div class="header-branding">
            <a href="index.php?lang=<?= htmlspecialchars($lang) ?>" class="logo-link">
                <img src="images/logo.png" alt="<?= htmlspecialchars(translate('Mapa de Serviços Públicos de São Paulo', $lang)) ?>" class="logo">
            </a>
        </div>

        <div class="header-controls">
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

            <div class="accessibility-tools" role="group" aria-label="Ferramentas de acessibilidade">
                <button id="toggle-contraste" class="a11y-btn" aria-label="Alternar alto contraste">
                    <i class="fas fa-adjust" aria-hidden="true"></i>
                    <span class="btn-text"><?= htmlspecialchars(translate('Contraste', $lang)) ?></span>
                </button>
                
                <button id="toggle-darkmode" class="a11y-btn" aria-label="Alternar modo escuro">
                    <i class="fas fa-moon" aria-hidden="true"></i>
                    <span class="btn-text"><?= htmlspecialchars(translate('Modo Escuro', $lang)) ?></span>
                </button>
                
                <button id="font-increase" class="a11y-btn" aria-label="Aumentar tamanho da fonte">
                    <i class="fas fa-text-height" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Inclua isso no <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- JavaScript revisado -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    try {
        // Configurações de acessibilidade
        const applySetting = (key, className, element = document.body) => {
            if (localStorage.getItem(key) === 'true') {
                element.classList.add(className);
            }
        };

        // Aplicar configurações salvas
        applySetting('highContrast', 'high-contrast');
        applySetting('darkMode', 'dark-mode');
        applySetting('fontSize', 'large-font', document.documentElement);

        // Event listeners
        document.getElementById('toggle-contraste')?.addEventListener('click', () => {
            document.body.classList.toggle('high-contrast');
            localStorage.setItem('highContrast', document.body.classList.contains('high-contrast'));
        });

        document.getElementById('toggle-darkmode')?.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });

        document.getElementById('font-increase')?.addEventListener('click', () => {
            document.documentElement.classList.toggle('large-font');
            localStorage.setItem('fontSize', document.documentElement.classList.contains('large-font') ? 'true' : 'false');
        });

        // Troca de idioma
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const lang = this.getAttribute('data-lang');
                if (lang) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('lang', lang);
                    window.location.href = url.toString();
                }
            });
        });
    } catch (error) {
        console.error('Erro no script do header:', error);
    }
});
</script>
