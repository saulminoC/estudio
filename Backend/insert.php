<?php
// Conexión a la base de datos
require_once 'conexion.php';

// Crear un usuario administrador con contraseña encriptada
$nombre = 'Administrador';
$correo_electronico = 'admin@estudio.com';
$contraseña = 'admin123'; // Cambia la contraseña aquí
$rol = 'admin';

// Encriptar la contraseña con bcrypt
$contraseña_encriptada = password_hash($contraseña, PASSWORD_BCRYPT);

// Insertar el usuario en la base de datos
try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo_electronico, contraseña, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $correo_electronico, $contraseña_encriptada, $rol]);
    
    echo "Usuario administrador creado exitosamente.";
} catch (PDOException $e) {
    echo "Error al crear el usuario: " . $e->getMessage();
}
?>
