<?php
require_once 'controllers/Database.php';
require_once 'models/Usuario.php';

class PasswordController {
    private $db;
    private $usuarioModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }
    
    public function solicitar() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/producto/index');
            exit;
        }
        require_once 'views/password/solicitar.php';
    }
    
    public function verificar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/password/solicitar');
            exit;
        }
        
        $correo = trim($_POST['correo'] ?? '');
        
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Correo electrónico no válido';
            header('Location: ' . BASE_URL . '/password/solicitar');
            exit;
        }
        
        $usuario = $this->usuarioModel->obtenerPorCorreo($correo);
        
        if (!$usuario) {
            $_SESSION['error'] = 'No existe una cuenta con ese correo electrónico';
            header('Location: ' . BASE_URL . '/password/solicitar');
            exit;
        }
        
        $_SESSION['reset_correo'] = $correo;
        $_SESSION['reset_pregunta'] = $usuario['preguntaSeguridad'];
        
        require_once 'views/password/verificar.php';
    }
    
    public function nueva() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/password/solicitar');
            exit;
        }
        
        if (!isset($_SESSION['reset_correo']) || !isset($_SESSION['reset_pregunta'])) {
            header('Location: ' . BASE_URL . '/password/solicitar');
            exit;
        }
        
        $correo = $_SESSION['reset_correo'];
        $pregunta = $_SESSION['reset_pregunta'];
        $respuesta = trim($_POST['respuesta'] ?? '');
        
        if (empty($respuesta)) {
            $_SESSION['error'] = 'Debes proporcionar una respuesta';
            header('Location: ' . BASE_URL . '/password/verificar');
            exit;
        }
        
        $usuario = $this->usuarioModel->obtenerPorCorreo($correo);
        
        if (!$usuario || !password_verify(strtolower($respuesta), $usuario['respuestaSeguridad'])) {
            $_SESSION['error'] = 'Respuesta incorrecta';
            header('Location: ' . BASE_URL . '/password/verificar');
            exit;
        }
        
        $_SESSION['reset_usuario_id'] = $usuario['id'];
        
        require_once 'views/password/nueva.php';
    }
    
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/password/solicitar');
            exit;
        }
        
        if (!isset($_SESSION['reset_usuario_id'])) {
            header('Location: ' . BASE_URL . '/password/solicitar');
            exit;
        }
        
        $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
        $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
        
        if (empty($nuevaContrasena) || empty($confirmarContrasena)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: ' . BASE_URL . '/password/nueva');
            exit;
        }
        
        if (strlen($nuevaContrasena) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
            header('Location: ' . BASE_URL . '/password/nueva');
            exit;
        }
        
        if ($nuevaContrasena !== $confirmarContrasena) {
            $_SESSION['error'] = 'Las contraseñas no coinciden';
            header('Location: ' . BASE_URL . '/password/nueva');
            exit;
        }
        
        $idUsuario = $_SESSION['reset_usuario_id'];
        $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        
        if ($this->usuarioModel->actualizarContrasena($idUsuario, $contrasenaHash)) {
            unset($_SESSION['reset_correo']);
            unset($_SESSION['reset_pregunta']);
            unset($_SESSION['reset_usuario_id']);
            
            $_SESSION['mensaje'] = 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión';
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        } else {
            $_SESSION['error'] = 'Error al actualizar la contraseña';
            header('Location: ' . BASE_URL . '/password/nueva');
            exit;
        }
    }
}
?>