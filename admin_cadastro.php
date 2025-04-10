<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

require 'conexao.php';
require 'config.php'; // aqui está sua $apiKey

function getCoordinates($endereco, $apiKey, $pdo) {
    $check = $pdo->prepare("SELECT latitude, longitude FROM geocache WHERE endereco = ?");
    $check->execute([$endereco]);
    $cache = $check->fetch();
    if ($cache) return $cache;

    $url = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($endereco) . "&key=$apiKey&language=pt&limit=1";
    $res = file_get_contents($url);
    $dados = json_decode($res, true);

    if (isset($dados['results'][0]['geometry'])) {
        $lat = $dados['results'][0]['geometry']['lat'];
        $lng = $dados['results'][0]['geometry']['lng'];
        $pdo->prepare("INSERT INTO geocache (endereco, latitude, longitude) VALUES (?, ?, ?)")
            ->execute([$endereco, $lat, $lng]);
        return ['latitude' => $lat, 'longitude' => $lng];
    }
    return null;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta
    $nome      = $_POST['nome'];
    $rua       = $_POST['rua'];
    $bairro    = $_POST['bairro'];
    $cidade    = $_POST['cidade'];
    $estado    = $_POST['estado'];
    $tipo      = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $inicio    = $_POST['horario_inicio'];
    $fim       = $_POST['horario_fim'];
    $pt        = $_POST['agendamento_pt'];
    $es        = $_POST['agendamento_es'];
    $en        = $_POST['agendamento_en'];
    $categorias = $_POST['categorias'] ?? [];

    // Geocodificação
    $enderecoCompleto = "$rua, $bairro, $cidade, $estado";
    $coords = getCoordinates($enderecoCompleto, $apiKey, $pdo);
    $lat = $coords['latitude'] ?? null;
    $lng = $coords['longitude'] ?? null;

    // Inserção
    $stmt = $pdo->prepare("INSERT INTO servicos (nome_servico, rua, bairro, cidade, estado, tipo, descricao, horario_inicio, horario_fim, latitude, longitude, agendamento_pt, agendamento_es, agendamento_en)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $rua, $bairro, $cidade, $estado, $tipo, $descricao, $inicio, $fim, $lat, $lng, $pt, $es, $en]);

    $servico_id = $pdo->lastInsertId();
    if (!empty($categorias)) {
        $vincular = $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)");
        foreach ($categorias as $cat_id) {
            $vincular->execute([$servico_id, $cat_id]);
        }
    }

    $msg = "✅ Serviço cadastrado com sucesso!";
}

// Carregar categorias
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Serviço</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container">
    <h2>Cadastro de Serviço</h2>
    <?php if ($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>

    <form method="POST">
        <label>Nome do Serviço</label>
        <input type="text" name="nome" required>

        <label>Rua</label><input type="text" name="rua" required>
        <label>Bairro</label><input type="text" name="bairro" required>
        <label>Cidade</label><input type="text" name="cidade" required>
        <label>Estado</label><input type="text" name="estado" required>

        <label>Tipo</label>
        <input type="text" name="tipo" required>

        <label>Descrição</label>
        <textarea name="descricao" required></textarea>

        <label>Horário de Funcionamento</label>
        <input type="time" name="horario_inicio" required> até
        <input type="time" name="horario_fim" required>

        <label>Instruções para Agendamento (Português)</label>
        <textarea name="agendamento_pt" required></textarea>

        <label>Instrucciones de Agendamiento (Español)</label>
        <textarea name="agendamento_es"></textarea>

        <label>Booking Instructions (English)</label>
        <textarea name="agendamento_en"></textarea>

        <label>Categorias</label><br>
        <?php foreach ($categorias as $cat): ?>
            <label>
                <input type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>">
                <span style="color:<?= $cat['cor'] ?>; font-weight:bold;"><?= $cat['nome'] ?></span>
            </label><br>
        <?php endforeach; ?>

        <button type="submit">Salvar</button>
    </form>

    <div style="margin-top:20px;">
        <a href="admin_gerenciar.php" class="botao-voltar">← Voltar para gerenciamento</a>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
</body>
</html>
