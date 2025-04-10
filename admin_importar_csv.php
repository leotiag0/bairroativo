<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

$apiKey = 'SUA_CHAVE_OPENCAGE'; // Substitua pela sua chave de API
$msg = '';

function getCoordinates($endereco, $apiKey, $pdo) {
    $check = $pdo->prepare("SELECT latitude, longitude FROM geocache WHERE endereco = ?");
    $check->execute([$endereco]);
    $cache = $check->fetch();
    if ($cache) return $cache;

    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . urlencode($endereco) . '&key=' . $apiKey . '&language=pt&limit=1';
    $resposta = file_get_contents($url);
    $dados = json_decode($resposta, true);

    if (isset($dados['results'][0]['geometry'])) {
        $lat = $dados['results'][0]['geometry']['lat'];
        $lng = $dados['results'][0]['geometry']['lng'];

        $pdo->prepare("INSERT INTO geocache (endereco, latitude, longitude) VALUES (?, ?, ?)")
            ->execute([$endereco, $lat, $lng]);

        return ['latitude' => $lat, 'longitude' => $lng];
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $file = $_FILES['arquivo']['tmp_name'];

    if (($handle = fopen($file, "r")) !== FALSE) {
        fgetcsv($handle); // pula cabeçalho

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            [$nome, $endereco, $bairro, $tipo, $descricao, $inicio, $fim, $categorias] = $data;

            $coords = getCoordinates($endereco, $apiKey, $pdo);
            $lat = $coords['latitude'] ?? null;
            $lng = $coords['longitude'] ?? null;

            $stmt = $pdo->prepare("INSERT INTO servicos (nome_servico, endereco, bairro, tipo, descricao, horario_inicio, horario_fim, latitude, longitude)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $endereco, $bairro, $tipo, $descricao, $inicio, $fim, $lat, $lng]);

            $id = $pdo->lastInsertId();

            foreach (explode(",", $categorias) as $catId) {
                $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)")
                    ->execute([$id, (int)$catId]);
            }
        }

        fclose($handle);
        $msg = "✅ Importação concluída com sucesso.";
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
        button:hover {
            background: #218838;
        }
        .msg {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: green;
        }
        .back-link {
            margin-top: 20px;
            text-align: center;
        }
        .back-link a {
            text-decoration: none;
            color: #007bff;
        }
        footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 60px;
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
    <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit"><i class="fas fa-upload"></i> Importar</button>
    </form>

    <div class="back-link">
        <a href="admin_gerenciar.php"><i class="fas fa-arrow-left"></i> Voltar para gerenciamento</a>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo. Todos os direitos reservados.
</footer>

</body>
</html>
