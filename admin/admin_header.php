<!-- admin_header.php -->
<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>

<header style="background:#007bff; color:white; padding:10px 20px; display:flex; justify-content:space-between; align-items:center;">
    <div style="display:flex; align-items:center; gap:10px;">
        <img src="images/logo.png" alt="Logo" style="height:50px;">
        <div class="header-left">
            <a href="../index.php?lang=<?= $lang ?>">
            <img src="images/logo.png" alt="Logo" class="logo">
        </a>
    </div>
        <span style="font-size:20px; font-weight:bold;">Administração - Bairro Ativo</span>
    </div>
    <nav style="display:flex; gap:20px;">
        <a href="admin_cadastro.php" class="btn">➕ Cadastrar</a>
        <a href="admin_gerenciar.php" class="btn">📂 Gerenciar</a>
        <a href="admin_importar_csv.php" class="btn">📥 Importar CSV</a>
        <a href="admin_estatisticas.php" class="btn">📊 Estatísticas</a>
        <a href="admin_logout.php" class="btn">🚪 Sair</a>
    </nav>
</header>
