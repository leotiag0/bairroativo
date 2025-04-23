<?php if (!isset($lang)) include 'lang.php'; ?>
 <header>
     <div class="header-left">
         <a href="index.php?lang=<?= $lang ?>">
             <img src="images/logo.png" alt="Logo" class="logo">
         </a>
     </div>
 
     <div class="header-right">
         <a href="?lang=pt"><img src="images/brasil-flag.jpg" class="flag-icon" alt="Português"></a>
         <a href="?lang=es"><img src="images/spain-flag.jpg" class="flag-icon" alt="Español"></a>
         <a href="?lang=en"><img src="images/uk-flag.jpg" class="flag-icon" alt="English"></a>
 
         <button id="toggle-contraste" title="Ativar/desativar alto contraste" class="btn">Alto Contraste</button>
         <button id="toggle-darkmode" class="btn">Modo Escuro</button>
     </div>
 </header>
 
 <script>
 document.addEventListener("DOMContentLoaded", function () {
     const botao = document.getElementById("toggle-contraste");
     const corpo = document.body;
 
     if (localStorage.getItem("contrasteAtivo") === "true") {
         corpo.classList.add("contraste-alto");
     }
 
     botao.addEventListener("click", () => {
         corpo.classList.toggle("contraste-alto");
         localStorage.setItem("contrasteAtivo", corpo.classList.contains("contraste-alto"));
     });
 });
 </script>
