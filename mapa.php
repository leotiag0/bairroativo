<?php
require 'conexao.php';
$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll(PDO::FETCH_ASSOC);
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos = $pdo->query("SELECT DISTINCT tipo FROM servicos ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Mapa de Serviços</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body, html { margin: 0; padding: 0; height: 100%; }
    #map { height: calc(100% - 120px); }
    .filtros {
      padding: 10px;
      background: #f1f1f1;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    select, input { padding: 8px; }
    @media (max-width: 768px) {
      .filtros { flex-direction: column; }
    }
  </style>
</head>
<body>

<div class="filtros">
  <input type="text" id="filtro-nome" placeholder="Buscar por nome...">
  <select id="filtro-bairro">
    <option value="">Todos os bairros</option>
    <?php foreach ($bairros as $bairro): ?>
      <option value="<?= htmlspecialchars($bairro) ?>"><?= $bairro ?></option>
    <?php endforeach; ?>
  </select>
  <select id="filtro-tipo">
    <option value="">Todos os tipos</option>
    <?php foreach ($tipos as $tipo): ?>
      <option value="<?= htmlspecialchars($tipo) ?>"><?= $tipo ?></option>
    <?php endforeach; ?>
  </select>
  <input type="time" id="hora-busca" placeholder="Disponível às">
  <button onclick="filtrar()">Filtrar</button>
  <button onclick="usarLocalizacao()">📍 Serviços próximos</button>
</div>

<div id="map"></div>

<script>
  const servicos = <?= json_encode($servicos, JSON_UNESCAPED_UNICODE); ?>;
  const map = L.map('map').setView([-23.55, -46.63], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  const markers = L.markerClusterGroup();
  let markerList = [], userLat, userLng, userMarker;

  const userIcon = L.icon({
    iconUrl: 'images/user-location.png',
    iconSize: [30, 30],
    iconAnchor: [15, 30],
    popupAnchor: [0, -30]
  });

  function criarMarcadores(lista) {
    markerList.forEach(m => markers.removeLayer(m));
    markerList = [];
    lista.forEach(s => {
      if (!s.latitude || !s.longitude) return;
      const popup = `
        <strong>${s.nome_servico}</strong><br>
        ${s.endereco}<br>${s.bairro}<br>${s.tipo}<br>${s.descricao}<br>
        ${userLat ? `📏 ${calcularDistancia(userLat, userLng, s.latitude, s.longitude).toFixed(2)} km de você<br>` : ''}
        ${userLat ? `
          <a href="https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${s.latitude},${s.longitude}&travelmode=driving" target="_blank">🚗</a>
          <a href="https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${s.latitude},${s.longitude}&travelmode=walking" target="_blank">🚶‍♂️</a>
          <a href="https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${s.latitude},${s.longitude}&travelmode=transit" target="_blank">🚌</a>` : ''
        }
      `;
      const marker = L.marker([s.latitude, s.longitude]);
      marker.bindPopup(popup);
      markers.addLayer(marker);
      markerList.push(marker);
    });
    map.addLayer(markers);
  }

  function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371, dLat = (lat2-lat1)*Math.PI/180, dLon = (lon2-lon1)*Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  }

  function filtrar() {
    const nome = document.getElementById('filtro-nome').value.toLowerCase();
    const bairro = document.getElementById('filtro-bairro').value.toLowerCase();
    const tipo = document.getElementById('filtro-tipo').value.toLowerCase();
    const hora = document.getElementById('hora-busca').value;

    const lista = servicos.filter(s => {
      return (!nome || s.nome_servico.toLowerCase().includes(nome)) &&
             (!bairro || s.bairro.toLowerCase() === bairro) &&
             (!tipo || s.tipo.toLowerCase() === tipo) &&
             (!hora || (s.horario_inicio <= hora && s.horario_fim >= hora));
    });
    criarMarcadores(lista);
  }

  function usarLocalizacao() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(pos => {
        userLat = pos.coords.latitude;
        userLng = pos.coords.longitude;
        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map).bindPopup("Você está aqui").openPopup();
        map.setView([userLat, userLng], 14);

        const proximos = servicos.filter(s => {
          if (!s.latitude || !s.longitude) return false;
          return calcularDistancia(userLat, userLng, s.latitude, s.longitude) <= 5;
        });
        criarMarcadores(proximos);
      }, () => alert("Erro ao localizar."));
    } else alert("Navegador sem suporte.");
  }

  criarMarcadores(servicos);
</script>

</body>
</html>
