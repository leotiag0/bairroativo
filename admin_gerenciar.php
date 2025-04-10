<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';
include 'admin_header.php';
?>

<link rel="stylesheet" href="css/admin.css">

<div class="container">
    <h2>Gerenciar Serviços</h2>

    <?php
    if (isset($_GET['excluir'])) {
        $id = (int) $_GET['excluir'];
        $pdo->prepare("DELETE FROM servicos WHERE id = ?")->execute([$id]);
        echo '<div class="msg">✅ Serviço excluído com sucesso.</div>';
    }

    $servicos = $pdo->query("SELECT * FROM servicos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Tipo</th>
                <th>Lat / Lng</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicos as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nome_servico']) ?></td>
                    <td><?= "{$s['rua']}, {$s['bairro']}, {$s['cidade']}/{$s['estado']}" ?></td>
                    <td><?= htmlspecialchars($s['tipo']) ?></td>
                    <td><?= $s['latitude'] ?>, <?= $s['longitude'] ?></td>
                    <td>
                        <a class="btn" href="admin_editar.php?id=<?= $s['id'] ?>">✏️ Editar</a>
                        <a class="btn" style="background:#dc3545;" href="?excluir=<?= $s['id'] ?>" onclick="return confirm('Tem certeza?')">🗑️ Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align:center;">
        <a class="botao-voltar" href="admin_cadastro.php">← Voltar ao cadastro</a>
    </div>
</div>

<?php include 'admin_footer.php'; ?>
