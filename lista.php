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
$cores = [];
foreach ($categorias as $cat) {
    $cores[$cat['id']] = $cat['cor'];
}

// Filtros recebidos
$where = [];
$params = [];
$order = 's.nome_servico ASC';

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
if (!empty($_GET['ordenar'])) {
    $ordem = $_GET['ordenar'];
    if (in_array($ordem, ['nome', 'tipo', 'bairro'])) {
        $order = "s.$ordem ASC";
    }
}

// Consulta
$sql = "
    SELECT s.*, c.nome AS categoria_nome, c.id AS categoria_id
    FROM servicos s
    LEFT JOIN servico_categoria sc ON sc.servico_id = s.id
    LEFT JOIN categorias c ON c.id = sc.categoria_id
";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> - Lista de Serviços</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($t['meta_descricao'] ?? 'Lista completa de serviços públicos disponíveis') ?>">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link rel="stylesheet" href="css/styles.css?v=<?= filemtime('css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    
    <script>
    // Armazena os filtros para compartilhamento entre páginas
    document.addEventListener('DOMContentLoaded', function() {
        // Restaura filtros se existirem
        if (sessionStorage.getItem('filtros')) {
            const filtros = JSON.parse(sessionStorage.getItem('filtros'));
            Object.keys(filtros).forEach(key => {
                const element = document.querySelector(`[name="${key}"]`);
                if (element) element.value = filtros[key];
            });
        }
        
        // Salva filtros ao mudar
        document.querySelectorAll('.filtros-grid input, .filtros-grid select').forEach(el => {
            el.addEventListener('change', function() {
                const formData = new FormData(document.querySelector('.filtros-grid'));
                const filtros = {};
                formData.forEach((value, key) => filtros[key] = value);
                sessionStorage.setItem('filtros', JSON.stringify(filtros));
            });
        });
    });
    </script>
</head>
<body>
<?php include 'header.php'; ?>

<main class="container">
    <h1 class="page-title"><?= $t['titulo'] ?> - Lista de Serviços</h1>
    
    <!-- Alternância entre visualizações -->
    <div class="view-toggle mb-3">
        <a href="lista.php?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-secundario active" aria-current="page">
            <i class="fas fa-list"></i> Lista
        </a>
        <a href="mapa.php?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-secundario">
            <i class="fas fa-map"></i> Mapa
        </a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="filtros-grid mb-4">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        
        <!-- Campo de busca -->
        <div class="filtro-grupo">
            <label for="busca" class="filtro-label"><?= $t['buscar'] ?></label>
            <input type="text" id="busca" name="q" placeholder="<?= $t['buscar_placeholder'] ?? 'Buscar serviços...' ?>" 
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>
        
        <!-- Bairro -->
        <div class="filtro-grupo">
            <label for="bairro" class="filtro-label">Bairro</label>
            <select id="bairro" name="bairro">
                <option value="">Todos os bairros</option>
                <?php foreach ($bairros as $bairro): ?>
                    <option value="<?= $bairro ?>" <?= ($_GET['bairro'] ?? '') === $bairro ? 'selected' : '' ?>>
                        <?= $bairro ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Tipo -->
        <div class="filtro-grupo">
            <label for="tipo" class="filtro-label">Tipo</label>
            <select id="tipo" name="tipo">
                <option value="">Todos os tipos</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= $tipo ?>" <?= ($_GET['tipo'] ?? '') === $tipo ? 'selected' : '' ?>>
                        <?= $tipo ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Categoria -->
        <div class="filtro-grupo">
            <label for="categoria" class="filtro-label">Categoria</label>
            <select id="categoria" name="categoria">
                <option value="">Todas as categorias</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($_GET['categoria'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                        <?= $c['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Ordenação -->
        <div class="filtro-grupo">
            <label for="ordenar" class="filtro-label">Ordenar por</label>
            <select id="ordenar" name="ordenar">
                <option value="nome" <?= ($_GET['ordenar'] ?? '') === 'nome' ? 'selected' : '' ?>>Nome (A-Z)</option>
                <option value="tipo" <?= ($_GET['ordenar'] ?? '') === 'tipo' ? 'selected' : '' ?>>Tipo</option>
                <option value="bairro" <?= ($_GET['ordenar'] ?? '') === 'bairro' ? 'selected' : '' ?>>Bairro</option>
            </select>
        </div>
        
        <!-- Ações -->
        <div class="filtro-acoes">
            <button type="submit" class="btn btn-primario">
                <i class="fas fa-search"></i> <?= $t['buscar'] ?>
            </button>
            <a href="lista.php?lang=<?= $lang ?>" class="btn btn-secundario">
                <i class="fas fa-undo"></i> Limpar
            </a>
            <button type="button" onclick="localizarUsuario()" class="btn btn-terciario">
                <i class="fas fa-location-arrow"></i> <?= $t['proximo'] ?? 'Perto de mim' ?>
            </button>
        </div>
    </form>
    
    <!-- Resultados -->
    <div class="resultados-header">
        <h2 id="resultados-titulo" class="sr-only">Resultados da busca</h2>
        <p class="resultados-contador">
            <?= count($servicos) ?> <?= $t['servicos_encontrados'] ?? 'serviços encontrados' ?>
        </p>
    </div>
    
    <?php if (count($servicos) === 0): ?>
        <div class="feedback feedback-info" role="alert">
            <i class="fas fa-info-circle"></i> Nenhum serviço encontrado com os filtros atuais.
        </div>
    <?php else: ?>
        <!-- Lista de serviços -->
        <div class="lista-servicos grid" role="list" aria-labelledby="resultados-titulo">
            <?php foreach ($servicos as $s): ?>
                <article class="service-card" role="listitem">
                    <div class="service-card__header">
                        <div class="flex items-center gap-2">
                            <span class="category-badge">
                                <span class="category-badge__color" style="background-color: <?= $cores[$s['categoria_id'] ?? '#999' ?>"></span>
                                <?= htmlspecialchars($s['categoria_nome'] ?? 'Geral') ?>
                            </span>
                            <span class="text-sm">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($s['bairro']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="service-card__body">
                        <h3 class="service-title">
                            <?= htmlspecialchars($s['nome_servico']) ?>
                        </h3>
                        
                        <div class="service-meta">
                            <p><i class="fas fa-tag"></i> <?= htmlspecialchars($s['tipo']) ?></p>
                            <?php if ($s['horario_inicio'] && $s['horario_fim']): ?>
                                <p><i class="far fa-clock"></i> <?= htmlspecialchars($s['horario_inicio']) ?> - <?= htmlspecialchars($s['horario_fim']) ?></p>
                            <?php endif; ?>
                            <?php if ($s['endereco']): ?>
                                <p><i class="fas fa-map-pin"></i> <?= htmlspecialchars($s['endereco']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="service-card__footer">
                        <a href="detalhes.php?id=<?= $s['id'] ?>&lang=<?= $lang ?>" class="btn btn-sm">
                            <i class="fas fa-info-circle"></i> <?= $t['detalhes'] ?>
                        </a>
                        <button class="btn btn-sm btn-secundario" onclick="mostrarNoMapa(<?= $s['latitude'] ?>, <?= $s['longitude'] ?>)">
                            <i class="fas fa-map-marked-alt"></i> Ver no mapa
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginação -->
        <nav class="paginacao" aria-label="Navegação de páginas">
            <ul class="paginacao-list">
                <li><a href="?pagina=1" aria-label="Primeira página">&laquo;</a></li>
                <li><a href="?pagina=1">1</a></li>
                <li><a href="?pagina=2" aria-current="page">2</a></li>
                <li><a href="?pagina=3">3</a></li>
                <li><span>...</span></li>
                <li><a href="?pagina=5" aria-label="Última página">&raquo;</a></li>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

<script>
// Funções compartilhadas
function localizarUsuario() {
    if (!navigator.geolocation) {
        mostrarFeedback("Seu navegador não suporta geolocalização", 'error');
        return;
    }
    
    const btn = document.querySelector('.btn-localizacao');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localizando...';
    btn.disabled = true;
    
    navigator.geolocation.getCurrentPosition(
        position => {
            // Redireciona para o mapa com a localização
            window.location.href = `mapa.php?lat=${position.coords.latitude}&lng=${position.coords.longitude}&lang=<?= $lang ?>`;
        },
        error => {
            const messages = {
                1: "Permissão para geolocalização negada.",
                2: "Não foi possível determinar sua localização.",
                3: "Tempo de espera esgotado."
            };
            mostrarFeedback(messages[error.code] || "Erro ao obter localização", 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}

function mostrarNoMapa(lat, lng) {
    sessionStorage.setItem('filtros', JSON.stringify({
        q: document.getElementById('busca').value,
        bairro: document.getElementById('bairro').value,
        tipo: document.getElementById('tipo').value,
        categoria: document.getElementById('categoria').value,
        ordenar: document.getElementById('ordenar').value
    }));
    window.location.href = `mapa.php?lat=${lat}&lng=${lng}&lang=<?= $lang ?>`;
}

function mostrarFeedback(mensagem, tipo = 'info') {
    const feedback = document.createElement('div');
    feedback.className = `feedback feedback-${tipo}`;
    feedback.setAttribute('role', 'alert');
    feedback.innerHTML = `<i class="fas fa-${tipo === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i> ${mensagem}`;
    
    document.body.appendChild(feedback);
    
    setTimeout(() => {
        feedback.classList.add('fade-out');
        setTimeout(() => feedback.remove(), 500);
    }, 5000);
}

// Atalhos de teclado
document.addEventListener('keydown', (e) => {
    if (['INPUT', 'SELECT', 'TEXTAREA'].includes(e.target.tagName)) return;
    
    switch(e.key) {
        case '1':
            window.location.href = 'lista.php?lang=<?= $lang ?>';
            break;
        case '2':
            window.location.href = 'mapa.php?lang=<?= $lang ?>';
            break;
        case '/':
            document.getElementById('busca').focus();
            break;
        case '?':
            mostrarAjudaTeclado();
            break;
    }
});

function mostrarAjudaTeclado() {
    const atalhos = [
        {tecla: '1', acao: 'Ir para a visualização de lista'},
        {tecla: '2', acao: 'Ir para a visualização de mapa'},
        {tecla: '/', acao: 'Focar no campo de busca'},
        {tecla: '?', acao: 'Mostrar esta ajuda'}
    ];
    
    const conteudo = atalhos.map(a => `<li><strong>${a.tecla}:</strong> ${a.acao}</li>`).join('');
    mostrarFeedback(`<h3>Atalhos do teclado</h3><ul>${conteudo}</ul>`, 'help');
}
</script>
</body>
</html>
