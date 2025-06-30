<?php
// guardar_reservas.php

// Mostrar errores para depuración (luego desactivar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Autoload de Composer (PHPMailer, etc.)
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Devolver siempre JSON
header('Content-Type: application/json; charset=UTF-8');

// 1) Configuración de conexión a MySQL
$host = 'localhost';
$db   = 'estudio';
$user = 'root';
$pass = '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: '.$e->getMessage()]);
    exit;
}

// 2) Leer y validar JSON de entrada
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}
foreach (['service','date','time','duration','price','clientName','clientEmail','clientPhone'] as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Falta el campo $field"]);
        exit;
    }
}

// 3) Insertar en la tabla `reservas`
$sql = "INSERT INTO reservas
    (servicio, fecha, hora, duracion, precio,
     nombre_cliente, email_cliente, telefono_cliente, notas)
  VALUES
    (:servicio, :fecha, :hora, :duracion, :precio,
     :nombre, :email, :telefono, :notas)";
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
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo guardar la reserva: '.$e->getMessage()]);
    exit;
}

// 4) Enviar correo de confirmación con PHPMailer
$mail = new PHPMailer(true);
try {
    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.tudominio.com';       // Cambia al host SMTP real
    $mail->SMTPAuth   = true;
    $mail->Username   = 'usuario@tudominio.com';    // Tu usuario SMTP
    $mail->Password   = 'tu_contraseña_smtp';       // Tu contraseña SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('info@studiolens.com', 'Studio Lens');
    $mail->addAddress($data['clientEmail'], $data['clientName']);

    // Contenido del correo
    $mail->isHTML(false);
    $mail->Subject = 'Tu reserva en Studio Lens ha sido confirmada';
    $mail->Body    = 
        "¡Hola {$data['clientName']}!\n\n".
        "Tu reserva ha sido confirmada con éxito.\n\n".
        "Detalles de tu reserva:\n".
        "- Servicio: {$data['service']}\n".
        "- Fecha: {$data['date']}\n".
        "- Hora: {$data['time']}\n".
        "- Duración: {$data['duration']} hora(s)\n".
        "- Precio: \${$data['price']}\n\n".
        "Gracias por elegir Studio Lens. ¡Te esperamos!\n";

    $mail->send();
} catch (Exception $e) {
    // No interrumpimos el flujo: la reserva ya está guardada
    error_log("Error enviando email: {$mail->ErrorInfo}");
}

// 5) Responder éxito
echo json_encode(['success' => true]);
