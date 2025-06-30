<?php
// get_blog.php
header('Content-Type: application/json; charset=UTF-8');

try {
    $pdo = new PDO(
      'mysql:host=localhost;dbname=estudio;charset=utf8mb4',
      'root','',
      [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    $stmt = $pdo->query("
      SELECT id,
             titulo AS title,
             contenido AS content,
             imagen AS image,
             categoria AS category,
             estado AS status,
             DATE_FORMAT(creado_en,'%d %b %Y') AS date
      FROM blog_posts
      ORDER BY creado_en DESC
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error'=>'Error al leer blog']);
}
