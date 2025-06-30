<?php
header('Content-Type: application/json; charset=UTF-8');
$pdo = new PDO("mysql:host=localhost;dbname=estudio;charset=utf8mb4",'root','',[
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
]);
$stmt = $pdo->query("SELECT * FROM paquetes WHERE estado='activo' ORDER BY creado_en DESC");
$pkgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($pkgs);
