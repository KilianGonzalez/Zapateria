<?php
require_once 'controllers/Database.php';
require_once 'models/Usuario.php';

class AuthController {
    private $db;
    private $usuarioModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }
    
    // Mostrar formulario de login
    public function login() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /producto/index');
            exit;
        }
        require_once 'views/usuario/login.php';
    }
    
    // Procesar login
    public function loginPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/login');
            exit;
        }
        
        $correo = $_POST['correo'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';
        
        if (empty($correo) || empty($contrasena)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: /auth/login');
            exit;
        }
        
        $usuario = $this->usuarioModel->obtenerPorCorreo($correo);
        
        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nom'];
            $_SESSION['admin'] = $usuario['admin'];
            
            header('Location: /producto/index');
            exit;
        } else {
            $_SESSION['error'] = 'Correo o contraseña incorrectos';
            header('Location: /auth/login');
            exit;
        }
    }
    
    // Mostrar formulario de registro
    public function registro() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /producto/index');
            exit;
        }
        require_once 'views/usuario/registro.php';
    }
    
    // Procesar registro
    public function registroPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/registro');
            exit;
        }
        
        $datos = [
            'nom' => $_POST['nombre'] ?? '',
            'cognom' => $_POST['apellido'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'contrasena' => $_POST['contrasena'] ?? '',
            'admin' => 'f'
        ];
        
        $contrasenaConfirm = $_POST['contrasena_confirm'] ?? '';
        
        // Validaciones
        if (empty($datos['nom']) || empty($datos['correo']) || empty($datos['contrasena'])) {
            $_SESSION['error'] = 'Los campos nombre, correo y contraseña son obligatorios';
            header('Location: /auth/registro');
            exit;
        }
        
        if ($datos['contrasena'] !== $contrasenaConfirm) {
            $_SESSION['error'] = 'Las contraseñas no coinciden';
            header('Location: /auth/registro');
            exit;
        }
        
        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Correo electrónico no válido';
            header('Location: /auth/registro');
            exit;
        }
        
        // Verificar si el correo ya existe
        if ($this->usuarioModel->obtenerPorCorreo($datos['correo'])) {
            $_SESSION['error'] = 'El correo ya está registrado';
            header('Location: /auth/registro');
            exit;
        }
        
        // Hash de la contraseña
        $datos['contrasena'] = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
        
        // Crear usuario
        if ($this->usuarioModel->crear($datos)) {
            $_SESSION['mensaje'] = 'Registro exitoso. Ahora puedes iniciar sesión';
            header('Location: /auth/login');
            exit;
        } else {
            $_SESSION['error'] = 'Error al registrar el usuario';
            header('Location: /auth/registro');
            exit;
        }
    }
    
    // Cerrar sesión
    public function logout() {
        session_destroy();
        header('Location: /producto/index');
        exit;
    }
}
?>
