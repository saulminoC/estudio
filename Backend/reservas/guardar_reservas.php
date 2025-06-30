<?php
// quitar cualquier espacio o línea en blanco antes del <?php

header('Content-Type: application/json; charset=UTF-8');

// 1) Configuración de conexión — AJUSTA estos valores a tu entorno WAMP
$host = 'localhost';        // o 'localhost'
$db   = 'estudio';
$user = 'root';
$pass = '';

// Data Source Name
$dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

// 2) Conectar con PDO
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    // Si falla la conexión, devolvemos JSON y salimos
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

// 3) Leer el JSON enviado por fetch()
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// 4) Preparar e insertar
$sql = "INSERT INTO reservas
    (servicio, fecha, hora, duracion, precio, nombre_cliente, email_cliente, telefono_cliente, notas)
  VALUES
    (:servicio, :fecha, :hora, :duracion, :precio, :nombre, :email, :telefono, :notas)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':servicio' => $data['service'],
        ':fecha'    => $data['date'],
        ':hora'     => $data['time'],
        ':duracion' => $data['duration'],
        ':precio'   => $data['price'],
        ':nombre'   => $data['clientName'],
        ':email'    => $data['clientEmail'],
        ':telefono' => $data['clientPhone'],
        ':notas'    => $data['clientNotes'] ?? null
    ]);

    // 5) Respuesta de éxito
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo guardar la reserva: ' . $e->getMessage()]);
}
