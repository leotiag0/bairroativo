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

function getCoordinates($endereco, $apiKey, $pdo) {
    $check = $pdo->prepare("SELECT latitude, longitude FROM geocache WHERE endereco = ?");
    $check->execute([trim($endereco)]);
    if ($cache = $check->fetch()) return $cache;

    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . urlencode($endereco) . '&key=' . $apiKey . '&language=pt&limit=1';
    
    try {
        $resposta = file_get_contents($url);
        if ($resposta === false) return ['latitude' => null, 'longitude' => null];
        
        $dados = json_decode($resposta, true);
        if (!isset($dados['results'][0]['geometry'])) return ['latitude' => null, 'longitude' => null];

        $lat = $dados['results'][0]['geometry']['lat'];
        $lng = $dados['results'][0]['geometry']['lng'];

        $pdo->prepare("INSERT INTO geocache (endereco, latitude, longitude) VALUES (?, ?, ?)")
           ->execute([trim($endereco), $lat, $lng]);

        return ['latitude' => $lat, 'longitude' => $lng];
    } catch (Exception $e) {
        error_log("Geocoding error: " . $e->getMessage());
        return ['latitude' => null, 'longitude' => null];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    if ($_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
        $error = "Erro no upload do arquivo. Código: " . $_FILES['arquivo']['error'];
    } else {
        $file = $_FILES['arquivo']['tmp_name'];
        $imported = 0;
        $skipped = 0;

        try {
            $pdo->beginTransaction();

            if (($handle = fopen($file, "r")) !== false) {
                // Verify header matches expected structure
                $header = fgetcsv($handle);
                $expectedHeader = ['nome_servico', 'endereco', 'bairro', 'tipo', 'descricao_pt', 
                                 'descricao_es', 'descricao_en', 'horario_inicio', 'horario_fim', 'categorias'];
                
                if ($header !== $expectedHeader) {
                    throw new Exception("Cabeçalho do CSV não corresponde ao formato esperado");
                }

                while (($data = fgetcsv($handle)) {
                    if (count($data) < 10) {
                        $skipped++;
                        continue;
                    }

                    // Clean and validate data
                    $data = array_map('trim', $data);
                    $data = array_map(function($item) {
                        return $item === '' ? null : $item;
                    }, $data);

                    // Extract all 10 fields exactly as per CSV structure
                    [
                        $nome_servico,
                        $endereco,
                        $bairro,
                        $tipo,
                        $descricao_pt,
                        $descricao_es,
                        $descricao_en,
                        $horario_inicio,
                        $horario_fim,
                        $categorias
                    ] = $data;

                    // Skip if required fields are empty
                    if (empty($nome_servico) || empty($endereco)) {
                        $skipped++;
                        continue;
                    }

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
                        $nome_servico, $endereco, $bairro, $tipo,
                        $descricao_pt, $descricao_es, $descricao_en,
                        $horario_inicio, $horario_fim,
                        $coords['latitude'], $coords['longitude']
                    ]);

                    $servico_id = $pdo->lastInsertId();

                    // Process categories if present
                    if (!empty($categorias)) {
                        $catStmt = $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)");
                        
                        foreach (explode(',', $categorias) as $catId) {
                            $catId = (int) trim($catId);
                            if ($catId > 0) {
                                $catStmt->execute([$servico_id, $catId]);
                            }
                        }
                    }

                    $imported++;
                }
                fclose($handle);
                $pdo->commit();

                $msg = sprintf(
                    "Importação concluída: %d registros importados, %d ignorados",
                    $imported,
                    $skipped
                );
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Erro na importação: " . $e->getMessage();
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
        /* [Previous CSS styles remain unchanged] */
        .csv-structure {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .csv-structure h4 {
            margin-top: 0;
            color: #007bff;
        }
        .csv-structure table {
            width: 100%;
            border-collapse: collapse;
        }
        .csv-structure th, .csv-structure td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .csv-structure th {
            background-color: #e9ecef;
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
    
    <div class="csv-structure">
        <h4>Estrutura do CSV exigida:</h4>
        <table>
            <thead>
                <tr>
                    <th>Coluna</th>
                    <th>Descrição</th>
                    <th>Exemplo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>nome_servico</td>
                    <td>Nome do serviço/local</td>
                    <td>Biblioteca Mário de Andrade</td>
                </tr>
                <tr>
                    <td>endereco</td>
                    <td>Endereço completo</td>
                    <td>Rua da Consolação, 94</td>
                </tr>
                <tr>
                    <td>bairro</td>
                    <td>Bairro onde está localizado</td>
                    <td>Consolação</td>
                </tr>
                <tr>
                    <td>tipo</td>
                    <td>Tipo de serviço/local</td>
                    <td>Biblioteca Pública</td>
                </tr>
                <tr>
                    <td>descricao_pt</td>
                    <td>Descrição em português</td>
                    <td>Principal biblioteca pública de São Paulo</td>
                </tr>
                <tr>
                    <td>descricao_es</td>
                    <td>Descrição em espanhol</td>
                    <td>Biblioteca principal de São Paulo</td>
                </tr>
                <tr>
                    <td>descricao_en</td>
                    <td>Descrição em inglês</td>
                    <td>Main public library in São Paulo</td>
                </tr>
                <tr>
                    <td>horario_inicio</td>
                    <td>Horário de abertura</td>
                    <td>09:00</td>
                </tr>
                <tr>
                    <td>horario_fim</td>
                    <td>Horário de fechamento</td>
                    <td>18:00</td>
                </tr>
                <tr>
                    <td>categorias</td>
                    <td>IDs de categorias separados por vírgula</td>
                    <td>21,23</td>
                </tr>
            </tbody>
        </table>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit"><i class="fas fa-upload"></i> Importar</button>
    </form>

    <div class="back-link">
        <a href="download_modelo.php"><i class="fas fa-file-csv"></i> Baixar modelo CSV</a>
        <a href="admin_gerenciar.php"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

</body>
</html>
