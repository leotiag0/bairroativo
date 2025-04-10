<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

$apiKey = '2923ef94f739425b96ec104bd6613eb5';
$msg = '';
$error = '';

// Improved geocaching function with error handling
function getCoordinates($endereco, $apiKey, $pdo) {
    // Check cache first
    $check = $pdo->prepare("SELECT latitude, longitude FROM geocache WHERE endereco = ?");
    $check->execute([trim($endereco)]);
    $cache = $check->fetch();
    if ($cache) return $cache;

    // If not in cache, call API
    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . urlencode($endereco) . '&key=' . $apiKey . '&language=pt&limit=1';
    
    try {
        $resposta = file_get_contents($url);
        if ($resposta === FALSE) {
            throw new Exception("Failed to call geocoding API");
        }
        
        $dados = json_decode($resposta, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response from API");
        }

        if (isset($dados['results'][0]['geometry'])) {
            $lat = $dados['results'][0]['geometry']['lat'];
            $lng = $dados['results'][0]['geometry']['lng'];

            // Store in cache
            $stmt = $pdo->prepare("INSERT INTO geocache (endereco, latitude, longitude) VALUES (?, ?, ?)");
            $stmt->execute([trim($endereco), $lat, $lng]);

            return ['latitude' => $lat, 'longitude' => $lng];
        }
    } catch (Exception $e) {
        error_log("Geocoding error for address $endereco: " . $e->getMessage());
    }

    return ['latitude' => null, 'longitude' => null];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    // Validate file
    if ($_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
        $error = "Erro no upload do arquivo: " . $_FILES['arquivo']['error'];
    } elseif ($_FILES['arquivo']['type'] !== 'text/csv') {
        $error = "Por favor, envie um arquivo CSV válido";
    } else {
        $file = $_FILES['arquivo']['tmp_name'];
        $importedCount = 0;
        $skippedCount = 0;

        try {
            $pdo->beginTransaction();

            if (($handle = fopen($file, "r")) !== FALSE) {
                // Skip header
                fgetcsv($handle);
                
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Skip empty or invalid lines
                    if (count($data) < 10 || empty($data[0]) || empty($data[1])) {
                        $skippedCount++;
                        continue;
                    }

                    // Trim all values
                    $data = array_map('trim', $data);

                    // Extract data with validation
                    [
                        $nome,
                        $endereco,
                        $bairro,
                        $tipo,
                        $descricao_pt,
                        $descricao_es,
                        $descricao_en,
                        $inicio,
                        $fim,
                        $categorias
                    ] = $data;

                    // Get coordinates
                    $coords = getCoordinates($endereco, $apiKey, $pdo);

                    // Insert service
                    $stmt = $pdo->prepare("INSERT INTO servicos (
                        nome_servico, endereco, bairro, tipo,
                        descricao_pt, descricao_es, descricao_en,
                        horario_inicio, horario_fim,
                        latitude, longitude
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    $stmt->execute([
                        $nome, $endereco, $bairro, $tipo,
                        $descricao_pt, $descricao_es, $descricao_en,
                        $inicio, $fim,
                        $coords['latitude'], $coords['longitude']
                    ]);

                    $id = $pdo->lastInsertId();

                    // Process categories
                    if (!empty($categorias)) {
                        $catIds = array_filter(
                            array_map('intval', explode(",", $categorias)),
                            function($catId) { return $catId > 0; }
                        );
                        
                        if (!empty($catIds)) {
                            $catStmt = $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)");
                            foreach ($catIds as $catId) {
                                $catStmt->execute([$id, $catId]);
                            }
                        }
                    }

                    $importedCount++;
                }

                fclose($handle);
                $pdo->commit();
                
                $msg = "✅ Importação concluída com sucesso. $importedCount registros importados";
                if ($skippedCount > 0) {
                    $msg .= ", $skippedCount registros ignorados (dados inválidos)";
                }
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erro durante a importação: " . $e->getMessage();
            error_log("CSV Import Error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Importar CSV - Bairro Ativo</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f5f5; }
        header {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header img { height: 50px; }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
        }
        .msg {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: green;
        }
        .error {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: #dc3545;
        }
        .back-link {
            margin-top: 20px;
            text-align: center;
        }
        .back-link a {
            text-decoration: none;
            color: #007bff;
            margin: 0 10px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 60px;
        }
        .file-requirements {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<header>
    <img src="images/logo.png" alt="Logo">
    <div>Administração - Importar Serviços CSV</div>
</header>

<div class="container">
    <h2><i class="fas fa-file-import"></i> Importar CSV</h2>
    
    <?php if ($msg): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="file-requirements">
        <h3>Requisitos do arquivo CSV:</h3>
        <ul>
            <li>Formato: nome,endereco,bairro,tipo,descricao_pt,descricao_es,descricao_en,horario_inicio,horario_fim,categorias</li>
            <li>Categorias devem ser IDs numéricos separados por vírgula</li>
            <li>Use o <a href="download_modelo.php">modelo CSV</a> como referência</li>
        </ul>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit"><i class="fas fa-upload"></i> Importar</button>
    </form>

    <div class="back-link">
        <a href="download_modelo.php"><i class="fas fa-file-csv"></i> Baixar modelo CSV</a>
        <a href="admin_gerenciar.php"><i class="fas fa-arrow-left"></i> Voltar para gerenciamento</a>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

</body>
</html>
