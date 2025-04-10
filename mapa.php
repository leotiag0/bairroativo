<?php
 include 'lang.php';
 require 'conexao.php';
 
 // Filtros
 $bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
 $tipos   = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
 
 // Condições de busca
 $where = [];
 $params = [];
 
 if (!empty($_GET['q'])) {
     $where[] = "(nome_servico LIKE :q OR tipo LIKE :q OR bairro LIKE :q OR cidade LIKE :q)";
     $params[':q'] = '%' . $_GET['q'] . '%';
 }
 if (!empty($_GET['bairro'])) {
     $where[] = "bairro = :bairro";
     $params[':bairro'] = $_GET['bairro'];
 }
 if (!empty($_GET['tipo'])) {
     $where[] = "tipo = :tipo";
     $params[':tipo'] = $_GET['tipo'];
 }
 
 $sql = "SELECT * FROM servicos";
 if ($where) {
     $sql .= " WHERE " . implode(" AND ", $where);
 }
 $stmt = $pdo->prepare($sql);
 $stmt->execute($params);
 $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
 ?>
 <!DOCTYPE html>
 <html lang="<?= $lang ?>">
 <head>
     <meta charset="UTF-8">
     <title><?= $t['titulo'] ?></title>
     <title>Mapa de Serviços</title>
     <link rel="stylesheet" href="css/public.css">
     <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
     <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
     <style>
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
         html, body { height: 100%; margin: 0; display: flex; flex-direction: column; }
         #map { flex: 1; min-height: 500px; }
         .filtros {
             padding: 15px;
             background: #f9f9f9;
             display: flex;
             flex-wrap: wrap;
             justify-content: center;
             gap: 10px;
         }
 
         .search-bar input[type="text"] {
             flex: 1 1 60%;
         .filtros select, .filtros button {
             padding: 10px;
             font-size: 14px;
             border-radius: 5px;
             border: none;
             font-size: 16px;
         }
 
         .search-bar button {
             padding: 10px 20px;
         .filtros button {
             background: #28a745;
             border: none;
             border-radius: 5px;
             color: white;
             font-size: 16px;
             border: none;
             cursor: pointer;
         }
 
         .search-bar button:hover {
         .filtros button:hover {
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
 
 <header>
     <img src="images/logo.png" alt="Logo">
     <nav class="menu">
         <a href="index.php?lang=<?= $lang ?>">🏠 Início</a>
     </nav>
     <div>
         <img src="images/logo.png" alt="Logo">
     </div>
     <form method="GET" action="mapa.php" style="display:flex; gap:10px;">
         <input type="hidden" name="lang" value="<?= $lang ?>">
         <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="<?= $t['buscar'] ?>..." style="padding:8px; border-radius:5px; border:none;">
         <button type="submit"><?= $t['buscar'] ?></button>
     </form>
 </header>
 
 <div class="search-bar">
     <form action="mapa.php" method="GET" style="display:flex; flex:1; max-width:900px; gap:10px;">
 <div class="filtros">
     <form method="GET" action="mapa.php" style="display:flex; flex-wrap:wrap; gap:10px;">
         <input type="hidden" name="lang" value="<?= $lang ?>">
         <input type="text" name="q" value="<?= htmlspecialchars($filtro) ?>" placeholder="<?= $t['buscar'] ?> por nome, bairro, tipo..." />
         <button type="submit"><?= $t['buscar'] ?></button>
         <select name="bairro">
             <option value="">Bairro</option>
             <?php foreach ($bairros as $b): ?>
                 <option value="<?= $b ?>" <?= ($_GET['bairro'] ?? '') === $b ? 'selected' : '' ?>><?= $b ?></option>
             <?php endforeach; ?>
         </select>
 
         <select name="tipo">
             <option value="">Tipo</option>
             <?php foreach ($tipos as $t): ?>
                 <option value="<?= $t ?>" <?= ($_GET['tipo'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
             <?php endforeach; ?>
         </select>
 
         <button type="submit">Filtrar</button>
     </form>
 
     <button onclick="localizarUsuario()">📍 Próximo de mim</button>
 </div>
 
 <div id="map"></div>
 
         L.marker([s.latitude, s.longitude]).addTo(map).bindPopup(popup);
     });
 
     function localizarUsuario() {
         if (!navigator.geolocation) {
             alert("Navegador não suporta geolocalização.");
             return;
         }
 
         navigator.geolocation.getCurrentPosition(function(pos) {
             const lat = pos.coords.latitude;
             const lng = pos.coords.longitude;
             L.marker([lat, lng], {
                 icon: L.icon({
                     iconUrl: 'https://cdn-icons-png.flaticon.com/512/64/64113.png',
                     iconSize: [32, 32],
                     iconAnchor: [16, 32]
                 })
             }).addTo(map).bindPopup("📍 Você está aqui").openPopup();
 
             map.setView([lat, lng], 15);
         }, function() {
             alert("Não foi possível obter sua localização.");
         });
     }
 </script>
 
 </body>
