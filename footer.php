<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.  
</footer>

      <script>
    document.addEventListener("DOMContentLoaded", function () {
        const btnContraste = document.getElementById("toggle-contraste");
    
        if (localStorage.getItem('contraste') === '1') {
            document.body.classList.add('contraste-alto');
        }
    
        btnContraste?.addEventListener("click", function () {
            document.body.classList.toggle('contraste-alto');
            const ativo = document.body.classList.contains('contraste-alto') ? '1' : '0';
            localStorage.setItem('contraste', ativo);
        });
    });
    </script>
