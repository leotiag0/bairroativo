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
    // Validação dos dados
    $nome      = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $rua       = filter_input(INPUT_POST, 'rua', FILTER_SANITIZE_STRING);
    $bairro    = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
    $cidade    = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
    $estado    = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
    $tipo      = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    $inicio    = $_POST['horario_inicio'];
    $fim       = $_POST['horario_fim'];
    $pt        = filter_input(INPUT_POST, 'agendamento_pt', FILTER_SANITIZE_STRING);
    $es        = filter_input(INPUT_POST, 'agendamento_es', FILTER_SANITIZE_STRING);
    $en        = filter_input(INPUT_POST, 'agendamento_en', FILTER_SANITIZE_STRING);
    $categorias = $_POST['categorias'] ?? [];

    // Geocodificação
    $enderecoCompleto = "$rua, $bairro, $cidade, $estado";
    $coords = getCoordinates($enderecoCompleto, $apiKey, $pdo);
    $lat = $coords['latitude'] ?? null;
    $lng = $coords['longitude'] ?? null;

    // Inserção
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO servicos (nome_servico, rua, bairro, cidade, estado, tipo, descricao, horario_inicio, horario_fim, latitude, longitude, agendamento_pt, agendamento_es, agendamento_en)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $rua, $bairro, $cidade, $estado, $tipo, $descricao, $inicio, $fim, $lat, $lng, $pt, $es, $en]);

        $servico_id = $pdo->lastInsertId();
        
        if (!empty($categorias)) {
            $vincular = $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)");
            foreach ($categorias as $cat_id) {
                $vincular->execute([$servico_id, (int)$cat_id]);
            }
        }
        
        $pdo->commit();
        $msg = "<div class='alert success'>✅ Serviço cadastrado com sucesso!</div>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $msg = "<div class='alert error'>Erro ao cadastrar: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Carregar categorias
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Serviço</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
    <?php include '/header.php'; ?>

    <div class="container">
        <h2>Cadastro de Serviço</h2>
        <?php if ($msg) echo $msg; ?>

        <form method="POST" class="form-cadastro">
            <div class="form-group">
                <label>Nome do Serviço *</label>
                <input type="text" name="nome" required class="form-control">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Rua *</label>
                    <input type="text" name="rua" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Bairro *</label>
                    <input type="text" name="bairro" required class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Cidade *</label>
                    <input type="text" name="cidade" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Estado *</label>
                    <input type="text" name="estado" required class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Tipo *</label>
                <input type="text" name="tipo" required class="form-control">
            </div>

            <div class="form-group">
                <label>Descrição *</label>
                <textarea name="descricao" required class="form-control" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Horário de Abertura *</label>
                    <input type="time" name="horario_inicio" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Horário de Fechamento *</label>
                    <input type="time" name="horario_fim" required class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Instruções para Agendamento (Português) *</label>
                <textarea name="agendamento_pt" required class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label>Instrucciones de Agendamiento (Español)</label>
                <textarea name="agendamento_es" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label>Booking Instructions (English)</label>
                <textarea name="agendamento_en" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-group">
                <label>Categorias</label>
                <div class="categorias-list">
                    <?php foreach ($categorias as $cat): ?>
                        <label class="categoria-item">
                            <input type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>">
                            <span style="color:<?= $cat['cor'] ?>"><?= htmlspecialchars($cat['nome']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar Serviço</button>
                <a href="/admin/admin_gerenciar.php" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>