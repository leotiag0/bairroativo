<?php if (!isset($lang)) include 'lang.php'; ?>
<header class="site-header">
    <div class="header-left">
        <a href="index.php?lang=<?= $lang ?>"><img src="images/logo.png" alt="Logo" class="logo"></a>
    </div>

    <div class="header-right">
        <div class="flags">
            <a href="?lang=pt"><img src="images/brasil-flag.jpg" class="flag-icon" alt="Português"></a>
            <a href="?lang=es"><img src="images/spain-flag.jpg" class="flag-icon" alt="Español"></a>
            <a href="?lang=en"><img src="images/uk-flag.jpg" class="flag-icon" alt="English"></a>
        </div>

        <button id="toggle-contraste" title="Alternar alto contraste">🌓 Contraste</button>
    </div>
</header>

<!-- CSS principal e tema escuro -->
<link rel="stylesheet" href="css/public.css?v=1.3">
<link rel="stylesheet" href="css/public-dark.css" id="dark-css" disabled>

<!-- Script de contraste -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const darkCss = document.getElementById('dark-css');
    const contrasteAtivo = localStorage.getItem('contrasteAtivo') === 'true';

    if (contrasteAtivo) {
        document.body.classList.add('contraste-alto');
        if (darkCss) darkCss.disabled = false;
    }

    const btnContraste = document.getElementById('toggle-contraste');
    if (btnContraste) {
        btnContraste.addEventListener('click', () => {
            document.body.classList.toggle('contraste-alto');
            const ativo = document.body.classList.contains('contraste-alto');
            localStorage.setItem('contrasteAtivo', ativo);
            if (darkCss) darkCss.disabled = !ativo;
        });
    }
});
</script>
