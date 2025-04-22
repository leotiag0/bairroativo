<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'lang.php';
require 'conexao.php';

// Filtros
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
$categorias = $pdo->query("SELECT id, nome, cor FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Mapeia cores
$categorias_cores = [];
foreach ($categorias as $cat) {
    $categorias_cores[$cat['id']] = $cat['cor'];
}

// Filtros dinâmicos
$where = [];
$params = [];
$join_categoria = '';

if (!empty($_GET['q'])) {
    $where[] = "(s.nome_servico LIKE :q OR s.tipo LIKE :q OR s.bairro LIKE :q OR s.cidade LIKE :q)";
    $params[':q'] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['bairro'])) {
    $where[] = "s.bairro = :bairro";
    $params[':bairro'] = $_GET['bairro'];
}
if (!empty($_GET['tipo'])) {
    $where[] = "s.tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}
if (!empty($_GET['categoria'])) {
    $where[] = "sc.categoria_id = :categoria";
    $params[':categoria'] = $_GET['categoria'];
}

// Consulta
$sql = "SELECT s.*, sc.categoria_id FROM servicos s
        LEFT JOIN servico_categoria sc ON sc.servico_id = s.id";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " GROUP BY s.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSONs
$json_servicos = json_encode($servicos, JSON_UNESCAPED_UNICODE);
$json_cores = json_encode($categorias_cores);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> | Bairro Ativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($t['meta_descricao'] ?? 'Mapa interativo de serviços públicos') ?>">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link rel="stylesheet" href="css/public.css?v=<?= filemtime('css/public.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <div class="filtros">
        <form method="GET" class="filtros-flex">
            <input type="hidden" name="lang" value="<?= $lang ?>">
            <label for="busca" class="sr-only"><?= $t['buscar'] ?></label>
            <input type="text" id="busca" name="q" placeholder="<?= $t['buscar'] ?>..." 
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                   aria-label="<?= $t['buscar'] ?> serviços">
            
            <label for="bairro-select" class="sr-only">Filtrar por bairro</label>
            <select id="bairro-select" name="bairro" aria-label="Selecione um bairro">
                <option value="">Todos os bairros</option>
                <?php foreach ($bairros as $bairro): ?>
                    <option value="<?= $bairro ?>" <?= ($_GET['bairro'] ?? '') === $bairro ? 'selected' : '' ?>>
                        <?= $bairro ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="tipo-select" class="sr-only">Filtrar por tipo</label>
            <select id="tipo-select" name="tipo" aria-label="Selecione um tipo de serviço">
                <option value="">Todos os tipos</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= $tipo ?>" <?= ($_GET['tipo'] ?? '') === $tipo ? 'selected' : '' ?>>
                        <?= $tipo ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="categoria-select" class="sr-only">Filtrar por categoria</label>
            <select id="categoria-select" name="categoria" aria-label="Selecione uma categoria">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($_GET['categoria'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                        <?= $c['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn" aria-label="Aplicar filtros">
                <i class="fas fa-search" aria-hidden="true"></i> <?= $t['buscar'] ?>
            </button>
            
            <button type="button" onclick="localizarUsuario()" class="btn btn-localizacao" 
                    aria-label="Localizar serviços próximos da minha posição">
                <i class="fas fa-location-arrow" aria-hidden="true"></i> <?= $t['proximo'] ?? 'Perto de mim' ?>
            </button>
        </form>
    </div>

    <div id="map" aria-label="Mapa interativo de serviços" role="application" tabindex="0">
        <div class="map-controls" role="group" aria-label="Controles do mapa">
            <button id="zoom-in" aria-label="Aumentar zoom">+</button>
            <button id="zoom-out" aria-label="Diminuir zoom">-</button>
            <button id="reset-view" aria-label="Resetar visualização">
                <i class="fas fa-home" aria-hidden="true"></i>
            </button>
        </div>
        
        <div id="map-legend" aria-labelledby="legend-title">
            <h3 id="legend-title">Legenda</h3>
            <div class="legend-items">
                <?php foreach ($categorias as $c): ?>
                <div class="legend-item" tabindex="0" role="button" 
                     aria-label="Filtrar por categoria <?= $c['nome'] ?>" 
                     onclick="filtrarPorCategoria(<?= $c['id'] ?>)"
                     onkeydown="if(event.key === 'Enter') filtrarPorCategoria(<?= $c['id'] ?>)">
                    <span class="legend-color" style="background-color: <?= $c['cor'] ?>"></span>
                    <span class="legend-label"><?= $c['nome'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div id="feedback-container" aria-live="polite"></div>
</main>

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
const servicos = <?= $json_servicos ?>;
const coresPorCategoria = <?= $json_cores ?>;
let map;
let userLocationMarker = null;
const feedbackContainer = document.getElementById('feedback-container');

// Configuração inicial do mapa
function initMap() {
    map = L.map('map', {
        zoomControl: false, // Usaremos nosso próprio controle
        keyboard: true,
        tap: false // Melhor para acessibilidade em dispositivos móveis
    }).setView([-23.55, -46.63], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18,
    }).addTo(map);
    
    // Adicionar marcadores
    loadMarkersInBatches(servicos);
    
    // Configurar eventos de teclado
    setupKeyboardNavigation();
}

// Adicionar marcador acessível
function addAccessibleMarker(servico) {
    if (!servico.latitude || !servico.longitude) return;
    
    const cor = coresPorCategoria[servico.categoria_id] || '#007bff';
    const marker = L.circleMarker([servico.latitude, servico.longitude], {
        radius: 8,
        fillColor: cor,
        color: cor,
        fillOpacity: 0.9,
        ariaLabel: `Serviço: ${servico.nome_servico} no bairro ${servico.bairro}`,
        keyboard: true,
        tabIndex: 0
    }).addTo(map);
    
    const popupContent = `
        <div role="dialog" aria-labelledby="popup-title-${servico.id}">
            <h3 id="popup-title-${servico.id}">${servico.nome_servico}</h3>
            <p>${servico.endereco}, ${servico.bairro}</p>
            <p><i class="fa fa-layer-group" aria-hidden="true"></i> ${servico.tipo}</p>
            <a href="detalhes.php?id=${servico.id}&lang=<?= $lang ?>" 
               aria-label="Mais detalhes sobre ${servico.nome_servico}">
                ℹ️ <?= htmlspecialchars($t['detalhes']) ?>
            </a>
        </div>
    `;
    
    marker.bindPopup(popupContent, {
        closeButton: true,
        autoClose: false,
        closeOnEscapeKey: true,
        className: 'accessible-popup'
    });
    
    // Evento para teclado
    marker.on('keypress', function(e) {
        if (e.originalEvent.key === 'Enter') {
            this.openPopup();
        }
    });
}

// Carregar marcadores em lotes para performance
function loadMarkersInBatches(servicos, batchSize = 50, delay = 100) {
    let processed = 0;
    
    function processBatch() {
        const batch = servicos.slice(processed, processed + batchSize);
        batch.forEach(addAccessibleMarker);
        processed += batchSize;
        
        if (processed < servicos.length) {
            setTimeout(processBatch, delay);
        } else {
            showFeedback("Todos os serviços foram carregados no mapa", 'success');
        }
    }
    
    processBatch();
}

// Localização do usuário
function localizarUsuario() {
    const geoButton = document.querySelector('.btn-localizacao');
    const originalText = geoButton.innerHTML;
    
    geoButton.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Localizando...';
    geoButton.disabled = true;
    
    if (!navigator.geolocation) {
        showFeedback("Seu navegador não suporta geolocalização", 'error');
        geoButton.innerHTML = originalText;
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        position => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Remover marcador anterior se existir
            if (userLocationMarker) {
                map.removeLayer(userLocationMarker);
            }
            
            // Adicionar novo marcador
            userLocationMarker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: 'images/user-location.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    ariaLabel: "Sua localização atual"
                }),
                keyboard: false // Não focável, já que temos o botão
            }).addTo(map).bindPopup("📍 Você está aqui").openPopup();
            
            map.setView([lat, lng], 14);
            
            // Carregar serviços próximos
            carregarServicosProximos(lat, lng);
            
            showFeedback("Localização encontrada! Mostrando serviços próximos", 'success');
            geoButton.innerHTML = originalText;
            geoButton.disabled = false;
        },
        error => {
            const messages = {
                1: "Permissão para geolocalização negada. Por favor, habilite nas configurações do seu navegador.",
                2: "Não foi possível determinar sua localização.",
                3: "Tempo de espera esgotado ao tentar obter localização."
            };
            showFeedback(messages[error.code] || "Erro ao obter localização", 'error');
            geoButton.innerHTML = originalText;
            geoButton.disabled = false;
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// Carregar serviços próximos via AJAX
function carregarServicosProximos(lat, lng) {
    fetch(`ajax/proximos.php?lat=${lat}&lng=${lng}&lang=<?= $lang ?>`)
        .then(response => {
            if (!response.ok) throw new Error('Erro na rede');
            return response.json();
        })
        .then(data => {
            if (data.length > 0) {
                data.forEach(addAccessibleMarker);
                showFeedback(`${data.length} serviços próximos encontrados`, 'success');
            } else {
                showFeedback("Nenhum serviço encontrado próximo à sua localização", 'info');
            }
        })
        .catch(() => {
            showFeedback("Erro ao buscar serviços próximos", 'error');
        });
}

// Filtrar por categoria
function filtrarPorCategoria(categoriaId) {
    const url = new URL(window.location.href);
    url.searchParams.set('categoria', categoriaId);
    window.location.href = url.toString();
}

// Mostrar feedback para o usuário
function showFeedback(message, type = 'info', timeout = 3000) {
    const feedback = document.createElement('div');
    feedback.className = `feedback feedback-${type}`;
    feedback.setAttribute('role', 'alert');
    feedback.innerHTML = message;
    
    feedbackContainer.appendChild(feedback);
    
    setTimeout(() => {
        feedback.classList.add('fade-out');
        setTimeout(() => feedback.remove(), 500);
    }, timeout);
}

// Configurar navegação por teclado
function setupKeyboardNavigation() {
    // Controles do mapa
    document.getElementById('zoom-in').addEventListener('click', () => map.zoomIn());
    document.getElementById('zoom-out').addEventListener('click', () => map.zoomOut());
    document.getElementById('reset-view').addEventListener('click', () => {
        map.setView([-23.55, -46.63], 12);
        showFeedback("Visualização resetada para a vista padrão", 'info');
    });
    
    // Atalhos globais
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') return;
        
        switch(e.key) {
            case '+':
                map.zoomIn();
                showFeedback("Zoom aumentado", 'info', 1000);
                break;
            case '-':
                map.zoomOut();
                showFeedback("Zoom diminuído", 'info', 1000);
                break;
            case '0':
                map.setView([-23.55, -46.63], 12);
                showFeedback("Visualização resetada", 'info', 1000);
                break;
            case 'l':
                document.querySelector('.btn-localizacao').focus();
                break;
            case 'f':
                document.querySelector('.filtros input[type="text"]').focus();
                break;
            case '?':
                showKeyboardHelp();
                break;
        }
    });
}

// Mostrar ajuda de teclado
function showKeyboardHelp() {
    const shortcuts = [
        {key: '+', action: 'Aumentar zoom'},
        {key: '-', action: 'Diminuir zoom'},
        {key: '0', action: 'Resetar visualização'},
        {key: 'l', action: 'Ir para botão de localização'},
        {key: 'f', action: 'Ir para campo de busca'},
        {key: '?', action: 'Mostrar esta ajuda'}
    ];
    
    const helpText = shortcuts.map(s => `<strong>${s.key}:</strong> ${s.action}`).join('<br>');
    showFeedback(`<h3>Atalhos do teclado</h3>${helpText}`, 'help', 5000);
}

// Ajustar altura do mapa dinamicamente
function ajustarAlturaMapa() {
    const header = document.querySelector('header');
    const filtros = document.querySelector('.filtros');
    const footer = document.querySelector('footer');
    const mapDiv = document.getElementById('map');
    
    const alturaMapa = window.innerHeight - (header?.offsetHeight || 0) - (filtros?.offsetHeight || 0) - (footer?.offsetHeight || 0);
    mapDiv.style.height = `${Math.max(alturaMapa, 400)}px`;
    
    if (map) map.invalidateSize();
}

// Inicialização
document.addEventListener("DOMContentLoaded", () => {
    ajustarAlturaMapa();
    initMap();
    
    // Verificar modo escuro
    if (localStorage.getItem('modo-escuro') === 'true') {
        document.body.classList.add('modo-escuro');
    }
    
    // Verificar permissões de geolocalização
    if (navigator.permissions) {
        navigator.permissions.query({name: 'geolocation'}).then(result => {
            if (result.state === 'granted') {
                showFeedback("Você já concedeu permissão para geolocalização. Clique no botão 'Perto de mim' para encontrar serviços próximos.", 'info', 5000);
            }
        });
    }
});

window.addEventListener('resize', ajustarAlturaMapa);
</script>
</body>
</html>
