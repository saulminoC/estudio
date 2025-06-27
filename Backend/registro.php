<?php
/**
 * Sistema de registro de usuarios
 * Archivo: registro.php
 */

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Configurar headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Iniciar la sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerar ID de sesión para prevenir session fixation
session_regenerate_id(true);

/**
 * Clase para manejar el registro de usuarios
 */
class UserRegistration {
    private $pdo;
    private $errors = [];
    
    public function __construct() {
        try {
            $this->pdo = getDB();
        } catch (Exception $e) {
            $this->addError("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Agregar error al array de errores
     */
    private function addError($message) {
        $this->errors[] = $message;
    }
    
    /**
     * Obtener todos los errores
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Verificar si hay errores
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Validar los datos del formulario
     */
    private function validateFormData($data) {
        // Verificar que todos los campos requeridos estén presentes
        $requiredFields = ['nombre', 'correo_electronico', 'contraseña'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->addError("El campo {$field} es requerido.");
            }
        }
        
        if ($this->hasErrors()) {
            return false;
        }
        
        // Validar nombre
        $nombre = sanitizeInput($data['nombre']);
        if (strlen($nombre) < 2) {
            $this->addError("El nombre debe tener al menos 2 caracteres.");
        }
        if (strlen($nombre) > 100) {
            $this->addError("El nombre no puede exceder 100 caracteres.");
        }
        
        // Validar email
        $email = sanitizeInput($data['correo_electronico']);
        if (!validateEmail($email)) {
            $this->addError("El formato del correo electrónico no es válido.");
        }
        
        // Validar contraseña
        $password = $data['contraseña'];
        if (strlen($password) < 8) {
            $this->addError("La contraseña debe tener al menos 8 caracteres.");
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $this->addError("La contraseña debe contener al menos una letra minúscula, una mayúscula y un número.");
        }
        
        return !$this->hasErrors();
    }
    
    /**
     * Verificar si el email ya existe
     */
    private function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE correo_electronico = ? LIMIT 1");
            $stmt->execute([$email]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar email: " . $e->getMessage());
            $this->addError("Error interno del servidor.");
            return true; // Por seguridad, asumimos que existe
        }
    }
    
    /**
     * Registrar nuevo usuario
     */
    public function registerUser($formData) {
        // Validar datos del formulario
        if (!$this->validateFormData($formData)) {
            return false;
        }
        
        $nombre = sanitizeInput($formData['nombre']);
        $email = sanitizeInput($formData['correo_electronico']);
        $password = $formData['contraseña'];
        $rol = 'cliente';  // Asignando el rol por defecto de "cliente"
        
        // Verificar si el email ya existe
        if ($this->emailExists($email)) {
            $this->addError("Este correo electrónico ya está registrado.");
            return false;
        }
        
        // Encriptar contraseña
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iteraciones
            'threads' => 3          // 3 hilos
        ]);
        
        // Insertar usuario en la base de datos
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO usuarios (nombre, correo_electronico, contraseña, rol) 
                 VALUES (?, ?, ?, ?)"
            );
            
            $result = $stmt->execute([$nombre, $email, $hashedPassword, $rol]);
            
            if ($result) {
                // Configurar sesión de usuario
                $_SESSION['usuario_id'] = $this->pdo->lastInsertId();
                $_SESSION['usuario'] = $nombre;
                $_SESSION['correo'] = $email;
                $_SESSION['rol'] = $rol;
                $_SESSION['login_time'] = time();
                
                return true;
            } else {
                $this->addError("Error al registrar el usuario.");
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error al insertar usuario: " . $e->getMessage());
            $this->addError("Error interno del servidor. Intente más tarde.");
            return false;
        }
    }
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar token CSRF (implementar en producción)
    // if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    //     die('Token CSRF inválido');
    // }
    
    $registration = new UserRegistration();
    
    if ($registration->registerUser($_POST)) {
        // Registro exitoso - redirigir
        header('Location: /estudio/index.html'); // Cambiar a la página principal
        exit;
    } else {
        // Mostrar errores
        $errors = $registration->getErrors();
        foreach ($errors as $error) {
            echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
        }
    }
} else {
    // Método no permitido
    http_response_code(405);
    echo "Método no permitido.";
}
?>

