<?php
include 'lang.php';
require 'conexao.php';

// Filtros únicos
$bairros = $pdo->query("SELECT DISTINCT bairro FROM servicos WHERE bairro IS NOT NULL ORDER BY bairro")->fetchAll(PDO::FETCH_COLUMN);
$tipos   = $pdo->query("SELECT DISTINCT tipo FROM servicos WHERE tipo IS NOT NULL ORDER BY tipo")->fetchAll(PDO::FETCH_COLUMN);
$categorias = $pdo->query("
    SELECT c.id, c.nome 
    FROM categorias c 
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

// Condições dinâmicas
$where = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = "(nome_servico LIKE :q OR tipo LIKE :q OR bairro LIKE :q OR cidade LIKE :q)";
    $params[':q'] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['bairro'])) {
    $where[] = "bairro = :bairro";
    $params[':bairro'] = $_GET['bairro'];
}
if (!empty($_GET['tipo'])) {
    $where[] = "tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}
if (!empty($_GET['categoria'])) {
    $where[] = "id IN (
        SELECT servico_id FROM servico_categoria WHERE categoria_id = :categoria
    )";
    $params[':categoria'] = $_GET['categoria'];
}

// Consulta
$sql = "SELECT * FROM servicos";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['titulo'] ?> | Bairro Ativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; color: #333; }

        header {
            background: #007bff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
           
