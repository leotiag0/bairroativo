<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir_ids']) && is_array($_POST['excluir_ids'])) {
        $ids = array_map('intval', $_POST['excluir_ids']);
        if (count($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM servicos WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            header("Location: admin_gerenciar.php?msg=excluidos&qt=" . count($ids));
            exit;
        }
    }
}

header("Location: admin_gerenciar.php");
exit;
