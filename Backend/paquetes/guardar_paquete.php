<?php
header('Content-Type: application/json; charset=UTF-8');

// Configuración de conexión
$host = 'localhost';
$db   = 'estudio';
$user = 'root';
$pass = '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error'=>'Conexión fallida: '.$e->getMessage()]);
  exit;
}

// Leer JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
  http_response_code(400);
  echo json_encode(['error'=>'Datos inválidos']);
  exit;
}

// Insertar
$sql = "INSERT INTO paquetes
  (nombre, descripcion, precio, duracion, incluye, estado)
 VALUES
  (:nombre, :descripcion, :precio, :duracion, :incluye, :estado)";
$stmt = $pdo->prepare($sql);

try {
  $stmt->execute([
    ':nombre'      => $data['nombre'],
    ':descripcion' => $data['descripcion'],
    ':precio'      => $data['precio'],
    ':duracion'    => $data['duracion'],
    ':incluye'     => $data['incluye'] ?? null,
    ':estado'      => $data['estado']
  ]);
  echo json_encode(['success'=>true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error'=>'No se guardó: '.$e->getMessage()]);
}
