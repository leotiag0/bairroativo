<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}

require 'conexao.php';
require 'config.php';

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
    $nome = $_POST['nome'] ?? '';
    $rua = $_POST['endereco'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $inicio = $_POST['horario_inicio'] ?? '';
    $fim = $_POST['horario_fim'] ?? '';
    $pt = $_POST['agendamento_pt'] ?? '';
    $es = $_POST['agendamento_es'] ?? '';
    $en = $_POST['agendamento_en'] ?? '';
    $categorias = $_POST['categorias'] ?? [];

    $enderecoCompleto = "$rua, $bairro, $cidade, $estado";
    $coords = getCoordinates($enderecoCompleto, $apiKey, $pdo);
    $lat = $coords['latitude'] ?? null;
    $lng = $coords['longitude'] ?? null;

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO servicos (nome_servico, endereco, bairro, cidade, estado, tipo, descricao, horario_inicio, horario_fim, latitude, longitude, agendamento_pt, agendamento_es, agendamento_en)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $rua, $bairro, $cidade, $estado, $tipo, $descricao, $inicio, $fim, $lat, $lng, $pt, $es, $en]);

        $servico_id = $pdo->lastInsertId();
        
        if (!empty($categorias)) {
            $vincular = $pdo->prepare("INSERT INTO servico_categoria (servico_id, categoria_id) VALUES (?, ?)");
            foreach ($categorias as $cat_id) {
                $vincular->execute([$servico_id, $cat_id]);
            }
        }
        
        $pdo->commit();
        $msg = "<div class='msg success'>✅ Serviço cadastrado com sucesso!</div>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $msg = "<div class='msg error'>Erro ao cadastrar: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Serviço</title>
    <link rel="stylesheet" href="css/admin.css?v=<?= filemtime('css/admin.css') ?>">
    </head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="container">
        <h2>Cadastro de Serviço</h2>
        <?php if ($msg) echo $msg; ?>

        <form method="POST" class="form-cadastro">
            <div class="form-group">
                <label>Nome do Serviço *</label>
                <input type="text" name="nome" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Rua *</label>
                    <input type="text" name="rua" required>
                </div>
                <div class="form-group">
                    <label>Bairro *</label>
                    <input type="text" name="bairro" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Cidade *</label>
                    <input type="text" name="cidade" required>
                </div>
                <div class="form-group">
                    <label>Estado *</label>
                    <input type="text" name="estado" required>
                </div>
            </div>

            <div class="form-group">
                <label>Tipo *</label>
                <input type="text" name="tipo" required>
            </div>

            <div class="form-group">
                <label>Descrição *</label>
                <textarea name="descricao" required rows="4"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Horário de Abertura *</label>
                    <input type="time" name="horario_inicio" required>
                </div>
                <div class="form-group">
                    <label>Horário de Fechamento *</label>
                    <input type="time" name="horario_fim" required>
                </div>
            </div>

            <div class="form-group">
                <label>Instruções para Agendamento (Português) *</label>
                <textarea name="agendamento_pt" required rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Instrucciones de Agendamiento (Español)</label>
                <textarea name="agendamento_es" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Booking Instructions (English)</label>
                <textarea name="agendamento_en" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Categorias</label>
                <div class="categorias-list">
                    <?php foreach ($categorias as $cat): ?>
                        <label class="categoria-item">
                            <input type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>">
                            <span style="color:<?= $cat['cor'] ?>"><?= $cat['nome'] ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="admin_gerenciar.php" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>

    <?php include 'admin_footer.php'; ?>
</body>
</html>
