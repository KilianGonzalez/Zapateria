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
    
    public function login() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/producto/index');
            exit;
        }
        require_once 'views/usuario/login.php';
    }
    
    public function loginPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        $correo = trim($_POST['correo'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        
        // Validación servidor
        if (empty($correo) || empty($contrasena)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Correo electrónico no válido';
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        $usuario = $this->usuarioModel->obtenerPorCorreo($correo);
        
        if ($usuario && password_verify($contrasena, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nom'];
            $_SESSION['admin'] = $usuario['admin'];
            
            header('Location: ' . BASE_URL . '/producto/index');
            exit;
        } else {
            $_SESSION['error'] = 'Correo o contraseña incorrectos';
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }
    
    public function registro() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/producto/index');
            exit;
        }
        require_once 'views/usuario/registro.php';
    }
    
    public function registroPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
        
        $datos = [
            'nom' => trim($_POST['nombre'] ?? ''),
            'cognom' => trim($_POST['apellido'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'contrasena' => $_POST['contrasena'] ?? '',
            'preguntaSeguridad' => $_POST['preguntaSeguridad'] ?? '',
            'respuestaSeguridad' => trim($_POST['respuestaSeguridad'] ?? ''),
            'admin' => 'f'
        ];
        
        $contrasenaConfirm = $_POST['contrasena_confirm'] ?? '';
        
        // Validaciones servidor
        if (empty($datos['nom']) || empty($datos['correo']) || empty($datos['contrasena'])) {
            $_SESSION['error'] = 'Los campos nombre, correo y contraseña son obligatorios';
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
        
        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Correo electrónico no válido';
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
        
        if (strlen($datos['contrasena']) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
        
        if ($datos['contrasena'] !== $contrasenaConfirm) {
            $_SESSION['error'] = 'Las contraseñas no coinciden';
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
        
        if (empty($datos['preguntaSeguridad']) || empty($datos['respuestaSeguridad'])) {
            $_SESSION['error'] = 'Debes seleccionar una pregunta de seguridad y proporcionar una respuesta';
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
        
        // Verificar si el correo ya existe
        if ($this->usuarioModel->verificarCorreoExiste($datos['correo'])) {
            $_SESSION['error'] = 'El correo ya está registrado';
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
        
        // Hash de contraseña y respuesta de seguridad
        $datos['contrasena'] = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
        $datos['respuestaSeguridad'] = password_hash(strtolower($datos['respuestaSeguridad']), PASSWORD_DEFAULT);
        
        if ($this->usuarioModel->crear($datos)) {
            $_SESSION['mensaje'] = 'Registro exitoso. Ahora puedes iniciar sesión';
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        } else {
            $_SESSION['error'] = 'Error al registrar el usuario';
            header('Location: ' . BASE_URL . '/auth/registro');
            exit;
        }
    }
    
    // AJAX: Verificar si el correo existe
    public function verificarCorreo() {
        if ($this->isAjax()) {
            $correo = trim($_POST['correo'] ?? '');
            
            if (empty($correo)) {
                echo json_encode(['existe' => false]);
                exit;
            }
            
            $existe = $this->usuarioModel->verificarCorreoExiste($correo);
            
            header('Content-Type: application/json');
            echo json_encode(['existe' => $existe]);
            exit;
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/producto/index');
        exit;
    }
    
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>