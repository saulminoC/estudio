<?php
header('Content-Type: application/json; charset=UTF-8');

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=estudio;charset=utf8mb4',
        'root','',
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    // Traer todas las reservas, ordenadas
    $stmt = $pdo->query("
        SELECT 
          nombre_cliente, email_cliente, telefono_cliente,
          servicio, fecha, hora, notas, creado_en
        FROM reservas
        ORDER BY fecha ASC, hora ASC
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>'Error al leer reservas']);
}
