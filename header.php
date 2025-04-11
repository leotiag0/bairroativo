<?php if (!isset($lang)) include 'lang.php'; ?>
<header>
    <div class="logo-area">
        <a href="index.php?lang=<?= $lang ?>">
            <img src="images/logo.png" alt="Logo Bairro Ativo" class="logo">
        </a>
    </div>
    <div class="right-items">
        <nav class="menu">
            <a href="?lang=pt"><img src="images/brasil-flag.jpg" alt="Português" class="flag-icon"></a>
            <a href="?lang=es"><img src="images/spain-flag.jpg" alt="Español" class="flag-icon"></a>
            <a href="?lang=en"><img src="images/uk-flag.jpg" alt="English" class="flag-icon"></a>
        </nav>
        <button id="toggle-contraste" title="Ativar alto contraste">🌓 Contraste</button>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggle-contraste');
    const current = localStorage.getItem('contrasteAtivo');

    if (current === 'true') {
        document.body.classList.add('contraste-alto');
    }

    toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('contraste-alto');
        localStorage.setItem('contrasteAtivo', document.body.classList.contains('contraste-alto'));
    });
});
</script>
