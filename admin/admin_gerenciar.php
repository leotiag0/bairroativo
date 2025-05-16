<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require '../conexao.php';
include 'admin_header.php';

// Filtros
$filtro_bairro = $_GET['bairro'] ?? '';
$filtro_tipo = $_GET['tipo'] ?? '';

// Lista bairros e tipos únicos
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);

// Filtros SQL
$where = [];
$params = [];

if ($filtro_bairro) {
    $where[] = "s.bairro = :bairro";
    $params[':bairro'] = $filtro_bairro;
}
if ($filtro_tipo) {
    $where[] = "s.tipo = :tipo";
    $params[':tipo'] = $filtro_tipo;
}

$sql = "
    SELECT s.*, 
           GROUP_CONCAT(c.nome ORDER BY c.nome SEPARATOR ', ') AS categorias
    FROM servicos s
    LEFT JOIN servico_categoria sc ON sc.servico_id = s.id
    LEFT JOIN categorias c ON c.id = sc.categoria_id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " GROUP BY s.id ORDER BY s.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Serviço</title>
    <link rel="stylesheet" href="css/admin.css?v=<?= time() ?>">
</head>

<div class="container">
    <h2>📋 Gerenciar Serviços</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'excluidos'): ?>
        <div class="msg">✅ <?= htmlspecialchars($_GET['qt']) ?> serviço(s) excluído(s) com sucesso.</div>
    <?php endif; ?>

    <p><?= count($servicos) ?> serviço(s) encontrado(s)</p>

    <!-- Filtros -->
    <form method="GET" class="filtros">
        <select name="bairro">
            <option value="">Todos os bairros</option>
            <?php foreach ($bairros as $bairro): ?>
                <option value="<?= htmlspecialchars($bairro) ?>" <?= $bairro === $filtro_bairro ? 'selected' : '' ?>>
                    <?= htmlspecialchars($bairro) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tipo">
            <option value="">Todos os tipos</option>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= htmlspecialchars($tipo) ?>" <?= $tipo === $filtro_tipo ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tipo) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn">🔍 Filtrar</button>
        <?php if ($filtro_bairro || $filtro_tipo): ?>
            <a href="admin_gerenciar.php" class="btn btn-danger">❌ Limpar filtros</a>
        <?php endif; ?>
    </form>

    <!-- Tabela com formulário para múltiplas exclusões -->
    <form method="POST" action="admin_excluir.php" onsubmit="return confirm('Deseja excluir os serviços selecionados?')">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleCheckboxes(this)"></th>
                        <th>Nome</th>
                        <th>Endereço</th>
                        <th>Bairro</th>
                        <th>Tipo</th>
                        <th>Categorias</th>
                        <th>Coordenadas</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicos as $s): ?>
                        <tr>
                            <td><input type="checkbox" name="excluir_ids[]" value="<?= $s['id'] ?>"></td>
                            <td data-label="Nome"><?= htmlspecialchars($s['nome_servico']) ?></td>
                            <td data-label="Endereço"><?= htmlspecialchars($s['endereco']) ?></td>
                            <td data-label="Bairro"><?= htmlspecialchars($s['bairro']) ?></td>
                            <td data-label="Tipo"><?= htmlspecialchars($s['tipo']) ?></td>
                            <td data-label="Categorias"><?= htmlspecialchars($s['categorias'] ?? '-') ?></td>
                            <td data-label="Coordenadas"><?= $s['latitude'] ?>, <?= $s['longitude'] ?></td>
                            <td data-label="Ações">
                                <a class="btn" href="admin_editar.php?id=<?= $s['id'] ?>">✏️ Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (count($servicos) > 0): ?>
            <div style="margin-top: 10px;">
                <button type="submit" class="btn btn-danger">🗑️ Excluir selecionados</button>
            </div>
        <?php endif; ?>
    </form>

    <div style="margin-top: 20px; text-align:center;">
        <a class="botao-voltar" href="admin_cadastro.php">← Voltar ao cadastro</a>
    </div>
</div>

<script>
function toggleCheckboxes(source) {
    const checkboxes = document.querySelectorAll('input[name="excluir_ids[]"]');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>

<?php include 'admin_footer.php'; ?>
</html>
