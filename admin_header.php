<!-- admin_header.php -->
<?php if (session_status() !== PHP_SESSION_ACTIVE) session_start(); ?>
<header style="background:#007bff; color:white; padding:10px 20px; display:flex; justify-content:space-between; align-items:center;">
    <div style="display:flex; align-items:center; gap:10px;">
        <img src="images/logo.png" alt="Logo" style="height:50px;">
        <span style="font-size:20px; font-weight:bold;">Administração - Bairro Ativo</span>
    </div>
    <nav style="display:flex; gap:20px;">
        <a href="admin_cadastro.php" style="color:white; text-decoration:none;">➕ Cadastrar</a>
        <a href="admin_gerenciar.php" style="color:white; text-decoration:none;">📂 Gerenciar</a>
        <a href="admin_importar_csv.php" style="color:white; text-decoration:none;">📥 Importar CSV</a>
        <a href="admin_logout.php" style="color:white; text-decoration:none;">🚪 Sair</a>
    </nav>
</header>
