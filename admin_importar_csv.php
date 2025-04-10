<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

$msg = '';
$error = '';

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
                fgetcsv($handle); // Using comma delimiter (default)
                
                while (($data = fgetcsv($handle)) !== FALSE) {
                    // Skip empty or invalid lines (expecting 14 columns)
                    if (count($data) < 14 || empty($data[0]) || empty($data[1])) {
                        $skippedCount++;
                        continue;
                    }

                    // Trim all values
                    $data = array_map('trim', $data);

                    // Extract data with validation
                    [
                        $nome_servico,
                        $rua,
                        $bairro,
                        $cidade,
                        $estado,
                        $tipo,
                        $descricao,
                        $horario_inicio,
                        $horario_fim,
                        $latitude,
                        $longitude,
                        $agendamento_pt,
                        $agendamento_es,
                        $agendamento_en
                    ] = $data;

                    // Validate coordinates
                    if (!is_numeric($latitude) || !is_numeric($longitude)) {
                        $skippedCount++;
                        continue;
                    }

                    // Insert service
                    $stmt = $pdo->prepare("INSERT INTO servicos (
                        nome_servico, endereco, bairro, cidade, estado, tipo,
                        descricao, horario_inicio, horario_fim,
                        latitude, longitude,
                        agendamento_pt, agendamento_es, agendamento_en
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    $stmt->execute([
                        $nome_servico, 
                        $rua, 
                        $bairro, 
                        $cidade, 
                        $estado, 
                        $tipo,
                        $descricao, 
                        $horario_inicio, 
                        $horario_fim,
                        $latitude, 
                        $longitude,
                        $agendamento_pt,
                        $agendamento_es,
                        $agendamento_en
                    ]);

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
            max-width: 800px;
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
            padding: 10px;
            background: #e8f5e9;
            border-radius: 4px;
        }
        .error {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: #dc3545;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
        }
        .back-link {
            margin-top: 20px;
            text-align: center;
        }
        .back-link a {
            text-decoration: none;
            color: #007bff;
            margin: 0 10px;
            display: inline-block;
            padding: 8px 15px;
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
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
            border-radius: 4px;
        }
        .file-requirements h3 {
            margin-top: 0;
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .file-requirements ul {
            padding-left: 20px;
            columns: 2;
            column-gap: 30px;
        }
        .file-requirements li {
            margin-bottom: 8px;
            break-inside: avoid;
        }
        @media (max-width: 600px) {
            .file-requirements ul {
                columns: 1;
            }
            .container {
                padding: 20px;
                margin: 20px;
            }
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
        <h3>Estrutura do arquivo CSV (separado por vírgulas):</h3>
        <ul>
            <li><strong>nome_servico</strong>: Nome do serviço</li>
            <li><strong>rua</strong>: Endereço completo</li>
            <li><strong>bairro</strong>: Bairro</li>
            <li><strong>cidade</strong>: Cidade</li>
            <li><strong>estado</strong>: Estado (sigla)</li>
            <li><strong>tipo</strong>: Tipo de serviço</li>
            <li><strong>descricao</strong>: Descrição em português</li>
            <li><strong>horario_inicio</strong>: Horário de abertura (HH:MM)</li>
            <li><strong>horario_fim</strong>: Horário de fechamento (HH:MM)</li>
            <li><strong>latitude</strong>: Coordenada geográfica (ex: -23.5505)</li>
            <li><strong>longitude</strong>: Coordenada geográfica (ex: -46.6333)</li>
            <li><strong>agendamento_pt</strong>: Info agendamento (PT)</li>
            <li><strong>agendamento_es</strong>: Info agendamento (ES)</li>
            <li><strong>agendamento_en</strong>: Info agendamento (EN)</li>
        </ul>
        <p style="margin-top: 15px; font-style: italic;">Dica: Use aspas para campos que contenham vírgulas (ex: "Serviço, com vírgula no nome")</p>
        <p style="margin-top: 10px;"><a href="download_modelo.php"><i class="fas fa-download"></i> Baixar modelo CSV</a></p>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit"><i class="fas fa-upload"></i> Importar</button>
    </form>

    <div class="back-link">
        <a href="download_modelo.php"><i class="fas fa-file-csv"></i> Baixar modelo</a>
        <a href="admin_gerenciar.php"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

</body>
</html>
