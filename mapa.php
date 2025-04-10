<?php
 include 'lang.php';
 require 'conexao.php';
 
 $filtro = $_GET['q'] ?? '';
 $stmt = $pdo->prepare("SELECT * FROM servicos WHERE 
     nome_servico LIKE :filtro OR 
     tipo LIKE :filtro OR 
     bairro LIKE :filtro OR 
     cidade LIKE :filtro");
 $stmt->execute([':filtro' => "%$filtro%"]);
 $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
 ?>
 <!DOCTYPE html>
 <html lang="<?= $lang ?>">
 <head>
     <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
     <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
     <style>
         #map { height: 500px; margin-bottom: 20px; }
         html, body {
             height: 100%;
             margin: 0;
             display: flex;
             flex-direction: column;
         }
 
         header {
             flex-shrink: 0;
         }
 
         .search-bar {
             flex-shrink: 0;
             padding: 10px 20px;
             background: #007bff;
             display: flex;
             flex-wrap: wrap;
             justify-content: center;
             gap: 10px;
         }
 
         .search-bar input[type="text"] {
             flex: 1 1 60%;
             padding: 10px;
             border-radius: 5px;
             border: none;
             font-size: 16px;
         }
 
         .search-bar button {
             padding: 10px 20px;
             background: #28a745;
             border: none;
             border-radius: 5px;
             color: white;
             font-size: 16px;
         }
 
         .search-bar button:hover {
             background: #218838;
         }
 
         #map {
             flex: 1;
             min-height: 400px;
         }
 
         footer {
             flex-shrink: 0;
             background: #007bff;
             color: white;
             text-align: center;
             padding: 15px;
         }
     </style>
 </head>
 <body>
 </header>
 
 <div class="search-bar">
     <form action="mapa.php" method="GET">
     <form action="mapa.php" method="GET" style="display:flex; flex:1; max-width:900px; gap:10px;">
         <input type="hidden" name="lang" value="<?= $lang ?>">
         <input type="text" name="q" value="<?= htmlspecialchars($filtro) ?>" placeholder="<?= $t['buscar'] ?> por nome, bairro, tipo..." />
         <button type="submit"><?= $t['buscar'] ?></button>
     </form>
 </div>
 
 <div class="container">
     <h2>🗺️ Mapa de Serviços</h2>
     <div id="map"></div>
 </div>
 <div id="map"></div>
 
 <footer>
     &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
