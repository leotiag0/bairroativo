<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.  
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('toggle-contraste');
    const storageKey = 'modoContraste';

    // Restaurar modo salvo
    if (localStorage.getItem(storageKey) === 'on') {
        document.body.classList.add('contraste-alto');
    }

    // Alternar modo
    btn.addEventListener('click', () => {
        document.body.classList.toggle('contraste-alto');

        if (document.body.classList.contains('contraste-alto')) {
            localStorage.setItem(storageKey, 'on');
        } else {
            localStorage.setItem(storageKey, 'off');
        }
    });
});
</script>

