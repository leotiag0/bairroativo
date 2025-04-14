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

// Função para buscar coordenadas com cache
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

// Processamento do arquivo
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
                $errors[] = "❌ Linha $linha: campos obrigatórios ausentes.";
                continue;
            }

            $cidade = "São Paulo";
            $estado = "SP";

            try {
                $coords = getCoordinates($endereco, $apiKey, $pdo);
                $lat = $coords['latitude'];
                $lng = $coords['longitude'];

                $stmt = $pdo->prepare("INSERT INTO servicos (
                    nome_servico, endereco, bairro, cidade, estado, tipo,
                    descricao_pt, descricao_es, descricao_en,
                    horario_inicio, horario_fim,
                    latitude, longitude
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $nome_servico, $endereco, $bairro, $cidade, $estado, $tipo,
                    $descricao_pt, $descricao_es, $descricao_en,
                    $horario_inicio, $horario_fim, $lat, $lng
                ]);

                $id = $pdo->lastInsertId();

                // Relacionar categorias
                foreach (explode(",", $categorias) as $catId) {
                    $catId = (int) trim($catId);
                    if ($catId > 0) {
                        $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)")
                            ->execute([$id, $catId]);
                    }
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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Impoortar Serviços</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<?php include 'admin_header.php';>
    
<body>

    <div>Administração - Importar Serviços CSV</div>

<div class="container">
    <h2><i class="fas fa-file-import"></i> Importar CSV</h2>

    <?php if ($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($errors): ?>
        <div class="error">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
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
        <p><b>Observação:</b> Todos os serviços serão registrados como <b>cidade = São Paulo</b> e <b>estado = SP</b>.</p>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".csv" required>
        <button type="submit"><i class="fas fa-upload"></i> Importar</button>
    </form>

    <div class="back-link">
        <a href="admin_gerenciar.php"><i class="fas fa-arrow-left"></i> Voltar ao gerenciamento</a>
    </div>
</div>

<?php include 'admin_footer.php';>

</body>
</html>
