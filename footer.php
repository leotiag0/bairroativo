<script>
function toggleContraste() {
    document.body.classList.toggle('contraste-alto');
    localStorage.setItem('contrasteAtivo', document.body.classList.contains('contraste-alto') ? '1' : '');
}

document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('contrasteAtivo') === '1') {
        document.body.classList.add('contraste-alto');
    }
});
</script>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.  
</footer>
