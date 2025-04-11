<?php if (!isset($lang)) include 'lang.php'; ?>
<header>
    <a href="index.php"><img src="images/logo.png" alt="Logo" class="logo"></a>
    
    <div class="header-right">
        <div class="flags">
            <a href="?lang=pt"><img src="images/brasil-flag.jpg" class="flag-icon" alt="Português"></a>
            <a href="?lang=es"><img src="images/spain-flag.jpg" class="flag-icon" alt="Español"></a>
            <a href="?lang=en"><img src="images/uk-flag.jpg" class="flag-icon" alt="English"></a>
        </div>
        <button id="toggle-contraste">Alto Contraste</button>
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
