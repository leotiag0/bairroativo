<?php
include 'lang.php';
require 'conexao.php';

// Filtros
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
$categorias = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Filtros recebidos
$where = [];
$params = [];
$order = 's.nome_servico ASC';

if (!empty($_GET['q'])) {
    $where[] = "(s.nome_servico LIKE :q OR s.tipo LIKE :q OR s.bairro LIKE :q OR s.cidade LIKE :q)";
    $params[':q'] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['bairro'])) {
    $where[] = "s.bairro = :bairro";
    $params[':bairro'] = $_GET['bairro'];
}
if (!empty($_GET['tipo'])) {
    $where[] = "s.tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}
if (!empty($_GET['categoria'])) {
    $where[] = "sc.categoria_id = :categoria";
    $params[':categoria'] = $_GET['categoria'];
}

if (!empty($_GET['ordenar'])) {
    $ordem = $_GET['ordenar'];
    if (in_array($ordem, ['nome', 'tipo', 'bairro'])) {
        $order = "s.$ordem ASC";
    }
}

// Consulta
$sql = "
    SELECT s.*, c.nome AS categoria_nome, c.id AS categoria_id
    FROM servicos s
    LEFT JOIN servico_categoria sc ON sc.servico_id = s.id
    LEFT JOIN categorias c ON c.id = sc.categoria_id
";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> - Lista</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/public.css">
</head>
<body>

<header>
    <a href="index.php"><img src="images/logo.png" alt="Logo" class="logo"></a>
    <nav class="menu">
        <a href="?lang=pt"><img src="images/brasil-flag.jpg" alt="Português" class="flag-icon"></a>
        <a href="?lang=es"><img src="images/spain-flag.jpg" alt="Español" class="flag-icon"></a>
        <a href="?lang=en"><img src="images/uk-flag.jpg" alt="English" class="flag-icon"></a>
    </nav>
</header>

    <button id="toggle-contraste">♿ Contraste</button>
<script>
document.getElementById('toggle-contraste').onclick = () => {
    document.body.classList.toggle('contraste-alto');
    localStorage.setItem('contrasteAtivo', document.body.classList.contains('contraste-alto'));
};
if (localStorage.getItem('contrasteAtivo') === 'true') {
    document.body.classList.add('contraste-alto');
}
</script>


<main class="container">
    <h2><?= $t['titulo'] ?> - Lista</h2>

    <!-- Filtros -->
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
        <input type="hidden" name="lang" value="<?= $lang ?>">
        <input type="text" name="q" placeholder="<?= $t['buscar'] ?>..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

        <select name="bairro">
            <option value="">Bairro</option>
            <?php foreach ($bairros as $bairro): ?>
                <option value="<?= $bairro ?>" <?= ($_GET['bairro'] ?? '') === $bairro ? 'selected' : '' ?>><?= $bairro ?></option>
            <?php endforeach; ?>
        </select>

        <select name="tipo">
            <option value="">Tipo</option>
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= $tipo ?>" <?= ($_GET['tipo'] ?? '') === $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
            <?php endforeach; ?>
        </select>

        <select name="categoria">
            <option value="">Categoria</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($_GET['categoria'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['nome'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="ordenar">
            <option value="nome" <?= ($_GET['ordenar'] ?? '') === 'nome' ? 'selected' : '' ?>>Ordenar por Nome</option>
            <option value="tipo" <?= ($_GET['ordenar'] ?? '') === 'tipo' ? 'selected' : '' ?>>Tipo</option>
            <option value="bairro" <?= ($_GET['ordenar'] ?? '') === 'bairro' ? 'selected' : '' ?>>Bairro</option>
        </select>

        <button type="submit" class="btn">🔍 <?= $t['buscar'] ?></button>
    </form>

    <!-- Lista de resultados -->
    <?php if (count($servicos) === 0): ?>
        <p>Nenhum serviço encontrado.</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($servicos as $s): ?>
                <div class="card-servico">
                    <div class="thumb">
                        <img src="images/categorias/<?= $s['categoria_id'] ?? '0' ?>.png" alt="<?= $s['categoria_nome'] ?? 'Categoria' ?>">
                    </div>
                    <div class="info">
                        <strong><?= htmlspecialchars($s['nome_servico']) ?></strong><br>
                        <span><?= htmlspecialchars($s['tipo']) ?> | <?= htmlspecialchars($s['bairro']) ?></span><br>
                        <a href="detalhes.php?id=<?= $s['id'] ?>&lang=<?= $lang ?>" class="btn" style="margin-top: 10px;">ℹ️ <?= $t['detalhes'] ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    &copy; <?= date('Y') ?> Sistema Bairro Ativo
</footer>

</body>
</html>
