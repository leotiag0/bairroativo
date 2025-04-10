<?php
require '../conexao.php';

header('Content-Type: application/json');

$lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
$lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);

if ($lat === false || $lng === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros inválidos.']);
    exit;
}

$sql = "
    SELECT *, 
    (6371 * acos(cos(radians(:lat)) * cos(radians(latitude)) *
    cos(radians(longitude) - radians(:lng)) + 
    sin(radians(:lat)) * sin(radians(latitude)))) AS distancia
    FROM servicos
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
    HAVING distancia <= 10
    ORDER BY distancia
    LIMIT 50
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':lat' => $lat, ':lng' => $lng]);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
