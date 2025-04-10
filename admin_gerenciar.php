<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';
include 'admin_header.php';

$servicos = $pdo->query("SELECT * FROM servicos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Serviços Cadastrados</h2>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Coordenadas</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicos as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nome_servico']) ?></td>
                    <td><?= "{$s['rua']}, {$s['bairro']}, {$s['cidade']}/{$s['estado']}" ?></td>
                    <td><?= $s['latitude'] ?>, <?= $s['longitude'] ?></td>
                    <td>
                        <a href="admin_editar.php?id=<?= $s['id'] ?>">✏️ Editar</a> |
                        <a href="?excluir=<?= $s['id'] ?>" onclick="return confirm('Excluir este serviço?')">🗑️ Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>
