<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

require 'conexao.php';

$apiKey = '2923ef94f739425b96ec104bd6613eb5'; // Substitua pela sua chave

function getCoordinates($endereco, $apiKey, $pdo) {
    $check = $pdo->prepare("SELECT latitude, longitude FROM geocache WHERE endereco = ?");
    $check->execute([$endereco]);
    $cache = $check->fetch();

    if ($cache) return $cache;

    $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($endereco) . "&key=$apiKey&language=pt&limit=1";
    $resposta = file_get_contents($url);
    $dados = json_decode($resposta, true);

    if (isset($dados['results'][0]['geometry'])) {
        $lat = $dados['results'][0]['geometry']['lat'];
        $lng = $dados['results'][0]['geometry']['lng'];

        $save = $pdo->prepare("INSERT INTO geocache (endereco, latitude, longitude) VALUES (?, ?, ?)");
        $save->execute([$endereco, $lat, $lng]);

        return ['latitude' => $lat, 'longitude' => $lng];
    }

    return null;
}

include 'admin_menu.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $bairro   = $_POST['bairro'];
    $tipo     = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $inicio   = $_POST['horario_inicio'];
    $fim      = $_POST['horario_fim'];
    $categorias = $_POST['categorias'] ?? [];

    $coords = getCoordinates($endereco, $apiKey, $pdo);
    $lat = $coords['latitude'] ?? null;
    $lng = $coords['longitude'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO servicos (nome_servico, endereco, bairro, tipo, descricao, horario_inicio, horario_fim, latitude, longitude)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $endereco, $bairro, $tipo, $descricao, $inicio, $fim, $lat, $lng]);

    $servico_id = $pdo->lastInsertId();

    if (!empty($categorias)) {
        $vincular = $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)");
        foreach ($categorias as $cat_id) {
            $vincular->execute([$servico_id, $cat_id]);
        }
    }

    $msg = "✅ Serviço cadastrado com sucesso!";
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Serviço</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; padding: 20px; }
        form { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        input, select, textarea { width: 100%; padding: 10px; margin-bottom: 15px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .msg { text-align: center; font-weight: bold; color: green; margin-bottom: 20px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Cadastro de Serviço</h2>

<?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

<form method="POST">
    <label>Nome do Serviço</label>
    <input type="text" name="nome" required>

    <label>Endereço</label>
    <input type="text" name="endereco" required>

    <label>Bairro</label>
    <input type="text" name="bairro" required>

    <label>Tipo</label>
    <input type="text" name="tipo" required>

    <label>Descrição</label>
    <textarea name="descricao" required></textarea>

    <label>Horário de Funcionamento</label>
    <input type="time" name="horario_inicio" required>
    até
    <input type="time" name="horario_fim" required>

    <label>Categorias (múltiplas)</label><br>
    <?php foreach ($categorias as $cat): ?>
        <label>
            <input type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>"> <?= $cat['nome'] ?>
        </label><br>
    <?php endforeach; ?>

    <br>
    <button type="submit">Cadastrar</button>
</form>

<p style="text-align:center; margin-top: 20px;">
    <a href="admin_gerenciar.php">← Voltar</a> | <a href="admin_logout.php">Sair</a>
</p>

</body>
</html>
