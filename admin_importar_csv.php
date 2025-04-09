<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

$apiKey = 'SUA_CHAVE_OPENCAGE'; // Troque pela sua chave
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
        fgetcsv($handle); // cabeçalho

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
        $msg = "Importação finalizada com sucesso.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Importar CSV</title>
</head>
<body>
    <h2>Importar Serviços</h2>
    <?php if ($msg): ?><p><?= $msg ?></p><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit">Importar</button>
    </form>
    <p><a href="admin_gerenciar.php">← Voltar</a></p>
</body>
</html>
