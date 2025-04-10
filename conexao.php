<?php
$host = 'srv881.hstgr.io';
$db   = 'u918328567_bairroativo';
$user = 'u918328567_ba1';
$pass = 'g/i7Div.&F8/njX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
