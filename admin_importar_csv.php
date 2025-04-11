<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

$apiKey = '2923ef94f739425b96ec104bd6613eb5';
$msg = '';
$errors = [];

function getCoordinates($endereco, $apiKey, $pdo) {
    $check = $pdo->prepare("SELECT latitude, longitude FROM geocache WHERE endereco = ?");
    $check->execute([$endereco]);
    $cache = $check->fetch();
    if ($cache) return $cache;

    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . urlencode($endereco . ', São Paulo, SP') . '&key=' . $apiKey . '&language=pt&limit=1';
    $resposta = @file_get_contents($url);
    $dados = json_decode($resposta, true);

    if (isset($dados['results'][0]['geometry'])) {
        $lat = $dados['results'][0]['geometry']['lat'];
        $lng = $dados['results'][0]['geometry']['lng'];

        $pdo->prepare("INSERT INTO geocache (endereco, latitude, longitude) VALUES (?, ?, ?)")
            ->execute([$endereco, $lat, $lng]);

        return ['latitude' => $lat, 'longitude' => $lng];
    }

    return ['latitude' => null, 'longitude' => null];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $file = $_FILES['arquivo']['tmp_name'];

    if (($handle = fopen($file, "r")) !== FALSE) {
        fgetcsv($handle); // pula cabeçalho
        $linha = 1;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $linha++;

            if (count($data) < 10) {
                $errors[] = "❌ Linha $linha: número insuficiente de colunas.";
                continue;
            }

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

            if (!$nome_servico || !$endereco || !$bairro || !$tipo || !$descricao_pt || !$horario_inicio || !$horario_fim || !$categorias) {
                $errors[] = "❌ Linha $linha: campos obrigatórios em branco.";
                continue;
            }

            $cidade = "São Paulo";
            $estado = "SP";

            try {
                $coords = getCoordinates($endereco, $apiKey, $pdo);
                $lat = $coords['latitude'];
                $lng = $coords['longitude'];

                $stmt = $pdo->prepare("INSERT INTO servicos (
                    nome_servico, rua, bairro, cidade, estado, tipo,
                    descricao_pt, descricao_es, descricao_en,
                    horario_inicio, horario_fim,
                    latitude, longitude
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


                $stmt->execute([
                    $nome_servico, $endereco, $bairro, $cidade, $estado, $tipo,
                    $agendamento_pt, $agendamento_es, $agendamento_en,
                    $horario_inicio, $horario_fim, $lat, $lng
                ]);

                $id = $pdo->lastInsertId();

                foreach (explode(",", $categorias) as $catId) {
                    $catId = (int) trim($catId);
                    if ($catId <= 0) {
                        $errors[] = "⚠️ Linha $linha: categoria inválida ($catId).";
                        continue;
                    }

                    $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)")
                        ->execute([$id, $catId]);
                }

            } catch (Exception $e) {
                $errors[] = "❌ Linha $linha: erro ao importar - " . $e->getMessage();
            }
        }

        fclose($handle);

        if (empty($errors)) {
            $msg = "✅ Importação concluída com sucesso.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Importar CSV - Bairro Ativo</title>
    <link rel="icon" href="images/logo.png">
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
            max-width: 700px;
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
        }
        button:hover { background: #218838; }
        .msg, .error { text-align: center; font-weight: bold; margin: 10px 0; }
        .msg { color: green; }
        .error { color: red; }
        .file-requirements {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 60px;
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
    </style>
</head>
<body>

<header>
    <img src="images/logo.png" alt="Logo">
    <div>Administração - Importar Serviços CSV</div>
</header>

<div class="container">
    <h2><i class="fas fa-file-import"></i> Importar CSV</h2>

    <?php if ($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $err): ?>
                <div><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="file-requirements">
        <strong>Formato esperado (10 colunas):</strong>
        <ol>
            <li>nome_servico</li>
            <li>endereco</li>
            <li>bairro</li>
            <li>tipo</li>
            <li>descricao_pt</li>
            <li>descricao_es</li>
            <li>descricao_en</li>
            <li>horario_inicio</li>
            <li>horario_fim</li>
            <li>categorias (IDs separados por vírgula)</li>
        </ol>
        <p><b>Obs:</b> Os serviços serão importados com cidade = São Paulo e estado = SP</p>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit"><i class="fas fa-upload"></i> Importar</button>
    </form>

    <div class="back-link">
        <a href="pre_visualizar_csv.php"><i class="fas fa-eye"></i> Pré-visualizar CSV</a>
        <a href="admin_gerenciar.php"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

</body>
</html>
