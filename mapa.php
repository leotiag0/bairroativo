<?php
require_once 'conexao.php';

// Obter todos os serviços
$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll(PDO::FETCH_ASSOC);

// Bairros e tipos únicos para filtros
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos   = $pdo->query("SELECT DISTINCT tipo FROM servicos ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Mapa de Serviços</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Leaflet + Cluster -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css">
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    body, html { margin: 0; padding: 0; height: 100%; font-family: Arial; }
    #map { height: calc(100% - 90px); }

    .filtros {
      padding: 10px;
      background: #f1f1f1;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }

    .filtros input, .filtros select {
      padding: 6px;
      font-size: 14px;
    }

    .filtros button {
      padding: 8px 14px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
    }

    @media (max-width: 768px) {
      .filtros {
        flex-direction: column;
        align-items: stretch;
      }
    }
  </style>
</head>
<body>

<!-- Filtros -->
<div class="filtros">
  <input type="text" id="filtro-nome" placeholder="Buscar por nome">
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
  <input type="time" id="hora-busca" title="Filtrar por horário">
  <button onclick="filtrar()">🔍 Filtrar</button>
  <button onclick="usarLocalizacao()">📍 Serviços próximos</button>
</div>

<!-- Mapa -->
<div id="map"></div>

<script>
  const servicos = <?= json_encode($servicos, JSON_UNESCAPED_UNICODE); ?>;
  let userLat, userLng, userMarker;

  const map = L.map('map').setView([-23.55, -46.63], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const markers = L.markerClusterGroup();
  let markerList = [];

  const userIcon = L.icon({
    iconUrl: 'images/user-location.png',
    iconSize: [30, 30],
    iconAnchor: [15, 30],
    popupAnchor: [0, -30]
  });

  function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2)**2;
    return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
  }

  function criarMarcadores(dados) {
    markers.clearLayers();
    markerList = [];

    dados.forEach(s => {
      if (!s.latitude || !s.longitude) return;

      let distancia = userLat ? calcularDistancia(userLat, userLng, s.latitude, s.longitude).toFixed(2) + ' km de você<br>' : '';
      let origem = userLat ? `${userLat},${userLng}` : '';
      let destino = `${s.latitude},${s.longitude}`;

      let rotas = origem ? `
        <a href="https://www.google.com/maps/dir/?api=1&origin=${origem}&destination=${destino}&travelmode=driving" target="_blank">🚗</a> 
        <a href="https://www.google.com/maps/dir/?api=1&origin=${origem}&destination=${destino}&travelmode=walking" target="_blank">🚶</a> 
        <a href="https://www.google.com/maps/dir/?api=1&origin=${origem}&destination=${destino}&travelmode=transit" target="_blank">🚌</a>` : '';

      const popup = `
        <strong>${s.nome_servico}</strong><br>
        <i class="fa fa-location-dot"></i> ${s.endereco}<br>
        <i class="fa fa-city"></i> ${s.bairro}<br>
        <i class="fa fa-layer-group"></i> ${s.tipo}<br>
        ${distancia}
        <i class="fa fa-clock"></i> ${s.horario_inicio} às ${s.horario_fim}<br>
        <i class="fa fa-info-circle"></i> ${s.descricao}<br><br>
        ${rotas}
      `;

      const m = L.marker([s.latitude, s.longitude]);
      m.bindPopup(popup);
      markers.addLayer(m);
    });

    map.addLayer(markers);
  }

  function filtrar() {
    const nome = document.getElementById('filtro-nome').value.toLowerCase();
    const bairro = document.getElementById('filtro-bairro').value.toLowerCase();
    const tipo = document.getElementById('filtro-tipo').value.toLowerCase();
    const hora = document.getElementById('hora-busca').value;

    const filtrados = servicos.filter(s => {
      const dentroNome = !nome || s.nome_servico.toLowerCase().includes(nome);
      const dentroBairro = !bairro || s.bairro.toLowerCase() === bairro;
      const dentroTipo = !tipo || s.tipo.toLowerCase() === tipo;
      const dentroHorario = !hora || (s.horario_inicio <= hora && s.horario_fim >= hora);

      return dentroNome && dentroBairro && dentroTipo && dentroHorario;
    });

    criarMarcadores(filtrados);
  }

  function usarLocalizacao() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(pos => {
        userLat = pos.coords.latitude;
        userLng = pos.coords.longitude;

        map.setView([userLat, userLng], 14);

        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.marker([userLat, userLng], { icon: userIcon })
                      .addTo(map)
                      .bindPopup("Você está aqui")
                      .openPopup();

        const proximos = servicos.filter(s => {
          if (!s.latitude || !s.longitude) return false;
          return calcularDistancia(userLat, userLng, s.latitude, s.longitude) <= 5;
        });

        criarMarcadores(proximos);
      }, () => alert("Não foi possível obter sua localização."));
    } else {
      alert("Seu navegador não suporta geolocalização.");
    }
  }

  // Carregamento inicial
  criarMarcadores(servicos);
</script>

</body>
</html>
