<?php 
// Verifica se a variável $lang está definida, caso contrário, inclui o arquivo de idioma
if (!isset($lang)) include 'lang.php'; 
?>
<header>
    <div class="header-left">
        <!-- Link para a página inicial com o idioma atual -->
        <a href="index.php?lang=<?= $lang ?>">
            <img src="images/logo.png" alt="Logo" class="logo">
        </a>
    </div>

    <div class="header-right">
        <!-- Links para alterar o idioma com as respectivas bandeiras -->
        <a href="?lang=pt"><img src="images/brasil-flag.jpg" class="flag-icon" alt="Português"></a>
        <a href="?lang=es"><img src="images/spain-flag.jpg" class="flag-icon" alt="Español"></a>
        <a href="?lang=en"><img src="images/uk-flag.jpg" class="flag-icon" alt="English"></a>

        <!-- Botão para alternar o modo de alto contraste -->
        <button id="toggle-contraste" class='btn' title="Ativar/desativar alto contraste">🌓 Contraste</button>
    </div>
</header>

<script>
// Aguarda o carregamento completo do DOM
document.addEventListener("DOMContentLoaded", function () {
    const botao = document.getElementById("toggle-contraste"); // Botão de alternância de contraste
    const corpo = document.body; // Corpo do documento

    // Verifica se o modo de alto contraste está ativado no armazenamento local
    if (localStorage.getItem("contrasteAtivo") === "true") {
        corpo.classList.add("contraste-alto"); // Adiciona a classe de alto contraste
    }

    // Adiciona um evento de clique ao botão para alternar o modo de contraste
    botao.addEventListener("click", () => {
        corpo.classList.toggle("contraste-alto"); // Alterna a classe de alto contraste
        // Atualiza o estado no armazenamento local
        localStorage.setItem("contrasteAtivo", corpo.classList.contains("contraste-alto"));
    });
});
</script>