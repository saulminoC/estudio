<?php
require_once 'conexion.php';

// Iniciar sesión
session_start();

// Verificar si los datos del formulario fueron enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Verificar que los datos no estén vacíos
    if (empty($email) || empty($password)) {
        die('Correo o contraseña no pueden estar vacíos.');
    }

    // Conectar a la base de datos
    try {
        $pdo = getDB();
        
        // Preparar la consulta SQL para verificar el correo electrónico
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo_electronico = ?");
        $stmt->execute([$email]);

        // Obtener el resultado de la consulta
        $user = $stmt->fetch();

        // Verificar si el usuario existe
        if ($user && password_verify($password, $user['contraseña'])) {
            // Contraseña válida, iniciar sesión
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario'] = $user['nombre'];
            $_SESSION['correo'] = $user['correo_electronico'];
            $_SESSION['rol'] = $user['rol'];
            
            // Redirigir según el rol del usuario
            if ($user['rol'] == 'admin') {
                // Si el usuario es administrador, redirigir a la página de administración
                header('Location: /estudio/Frontend/web/admin/inicio.html');
            } else {
                // Si el usuario es cliente, redirigir al inicio
                header('Location: /estudio/index.html');
            }
            exit;
        } else {
            echo "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error al conectar a la base de datos: " . $e->getMessage();
    }
}
?>
