<?php
require_once 'controllers/Database.php';
require_once 'models/Usuario.php';
require_once 'models/Pedido.php';

class UsuarioController {
    private $db;
    private $usuarioModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }
    
    public function perfil() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        
        $pedidoModel = new Pedido($this->db);
        $pedidos = $pedidoModel->obtenerPorUsuario($_SESSION['usuario_id']);
        
        require_once 'views/usuario/perfil.php';
    }
}
?>