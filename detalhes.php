<?php
// Inclui o arquivo de idioma
include 'lang.php';

// Requer o arquivo de conexão com o banco de dados
require 'conexao.php';

// Obtém o ID da URL e valida se é um número inteiro
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Verifica se o ID é inválido
if ($id === null || $id === false) {
    echo "ID inválido.";
    exit;
}

// Prepara a consulta para buscar o serviço pelo ID
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o serviço foi encontrado
if ($s) {
    // Sanitiza e valida os dados retornados
    $s = array_map('htmlspecialchars', $s);

    // Valida os campos obrigatórios
    $requiredFields = ['nome_servico', 'endereco', 'bairro', 'cidade', 'estado', 'descricao_pt', 'descricao_en', 'descricao_es'];
    foreach ($requiredFields as $field) {
        if (empty($s[$field])) {
            echo "Dados inválidos ou incompletos.";
            exit;
        }
    }

    // Valida latitude e longitude
    $s['latitude'] = filter_var($s['latitude'], FILTER_VALIDATE_FLOAT);
    $s['longitude'] = filter_var($s['longitude'], FILTER_VALIDATE_FLOAT);

    if ($s['latitude'] === false || $s['longitude'] === false) {
        echo "Coordenadas inválidas.";
        exit;
    }
} else {
    // Caso o serviço não seja encontrado
    echo "Serviço não encontrado.";
    exit;
}

// Define o idioma com base no parâmetro da URL ou usa 'pt' como padrão
$lang = $_GET['lang'] ?? 'pt';
$lang = in_array($lang, ['pt', 'en', 'es']) ? $lang : 'pt';

// Define a descrição com base no idioma selecionado
$descricao = $lang == 'es' ? $s['descricao_es'] : ($lang == 'en' ? $s['descricao_en'] : $s['descricao_pt']);

// Busca as categorias associadas ao serviço
$stmtCat = $pdo->prepare("
    SELECT c.nome, c.cor 
    FROM categorias c
    JOIN servico_categoria sc ON sc.categoria_id = c.id
    WHERE sc.servico_id = ?
");
$stmtCat->execute([$id]);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($s['nome_servico']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="css/public.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <?php include 'header.php'; ?>

<main class="container">
    <!-- Exibe o nome do serviço -->
    <h2><?= htmlspecialchars($s['nome_servico']) ?></h2>

    <?php
    // Valida a cor da categoria
    $validColor = preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $cat['cor']) ? $cat['cor'] : '#ccc';
    ?>
    <span class="categoria-badge" style="background: <?= htmlspecialchars($validColor) ?>;">
        <?= htmlspecialchars($cat['nome']) ?>
    </span>

    <!-- Exibe o endereço do serviço -->
    <p><strong>Local:</strong> <?= htmlspecialchars($s['endereco']) ?>, <?= htmlspecialchars($s['bairro']) ?>, <?= htmlspecialchars($s['cidade']) ?>/<?= htmlspecialchars($s['estado']) ?></p>

    <?php if (!empty($categorias)): ?>
        <?php
        // Valida latitude e longitude
        $latitude = is_numeric($s['latitude']) && $s['latitude'] >= -90 && $s['latitude'] <= 90 ? $s['latitude'] : null;
        $longitude = is_numeric($s['longitude']) && $s['longitude'] >= -180 && $s['longitude'] <= 180 ? $s['longitude'] : null;

        if ($latitude === null || $longitude === null) {
            echo "<p>Coordenadas inválidas para exibir o mapa.</p>";
        } else {
            echo '<div id="map"></div>';
        }
        ?>

        <!-- Exibe as categorias associadas -->
        <?php foreach ($categorias as $cat): ?>
            <span class="categoria-badge" style="background: <?= htmlspecialchars($cat['cor']) ?>;">
                <?= htmlspecialchars($cat['nome']) ?>
            </span>
        <?php endforeach; ?>
    <?php endif; ?>

    <div id="map"></div>

    <div class="btn-group">
        <!-- Link para rota no Google Maps -->
        <a class="btn" href="https://www.google.com/maps/dir/?destination=<?= $s['latitude'] ?>,<?= $s['longitude'] ?>" target="_blank">📍 <?= $t['rota'] ?></a>
        <?php if ($latitude !== null && $longitude !== null): ?>
        const map = L.map('map').setView([<?= $latitude ?>, <?= $longitude ?>], 16);
        <?php endif; ?>
    <?php
    // Valida latitude e longitude novamente
    $latitude = is_numeric($s['latitude']) && $s['latitude'] >= -90 && $s['latitude'] <= 90 ? $s['latitude'] : null;
    $longitude = is_numeric($s['longitude']) && $s['longitude'] >= -180 && $s['longitude'] <= 180 ? $s['longitude'] : null;

    if ($latitude === null || $longitude === null) {
        echo "<p>Coordenadas inválidas para exibir o mapa.</p>";
    } else {
        echo '<div id="map"></div>';
    }

    // Exibe as categorias novamente
    foreach ($categorias as $cat): ?>
        <span class="categoria-badge" style="background: <?= htmlspecialchars($cat['cor']) ?>;">
            <?= htmlspecialchars($cat['nome']) ?>
        </span>
    <?php endforeach; ?>
