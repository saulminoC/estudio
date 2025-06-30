<?php
// guardar_blog.php
header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors',1);
error_reporting(E_ALL);

// 1) Conexión
try {
    $pdo = new PDO(
      'mysql:host=localhost;dbname=estudio;charset=utf8mb4',
      'root','',
      [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error'=>'Conexión fallida: '.$e->getMessage()]);
    exit;
}

// 2) Leer JSON
$data = json_decode(file_get_contents('php://input'), true);
if (
  !$data ||
  empty($data['title']) ||
  empty($data['content'])
) {
    http_response_code(400);
    echo json_encode(['error'=>'Faltan campos requeridos']);
    exit;
}

// 3) Insertar
$sql = "INSERT INTO blog_posts
  (titulo, contenido, imagen, categoria, estado)
 VALUES
  (:t, :c, :i, :cat, :e)";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([
      ':t'   => $data['title'],
      ':c'   => $data['content'],
      ':i'   => $data['image']    ?? null,
      ':cat' => $data['category'] ?? null,
      ':e'   => $data['status']   ?? 'publicado'
    ]);
    echo json_encode(['success'=>true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error'=>'No se guardó: '.$e->getMessage()]);
}
