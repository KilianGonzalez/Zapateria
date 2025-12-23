<?php
require_once 'controllers/Database.php';
require_once 'models/Producto.php';
require_once 'models/ImagenProducto.php';
require_once 'models/TipoProducto.php';
require_once 'models/Marca.php';

class ProductoController {
    private $db;
    private $productoModel;
    private $imagenModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productoModel = new Producto($this->db);
        $this->imagenModel = new ImagenProducto($this->db);
    }
    
    public function index() {
        $productos = $this->productoModel->obtenerTodos();
        require_once 'views/productos/index.php';
    }
    
    public function detalle($id) {
        $producto = $this->productoModel->obtenerPorId($id);
        
        if (!$producto) {
            header('Location: ' . BASE_URL . '/producto/index');
            exit;
        }
        
        $imagenes = $this->imagenModel->obtenerPorProducto($id);
        require_once 'views/productos/detalle.php';
    }
    
    public function resultados() {
        $termino = $_GET['q'] ?? '';
        $productos = [];
        
        if (!empty($termino) && strlen($termino) >= 2) {
            $productos = $this->productoModel->buscar($termino);
        }
        
        require_once 'views/productos/resultados.php';
    }
    
    // AJAX: Obtener tipos de productos
    public function obtenerTipos() {
        if ($this->isAjax()) {
            $tipoModel = new TipoProducto($this->db);
            $tipos = $tipoModel->obtenerTodos();
            
            header('Content-Type: application/json');
            echo json_encode($tipos);
            exit;
        }
    }
    
    // AJAX: Obtener marcas
    public function obtenerMarcas() {
        if ($this->isAjax()) {
            $marcaModel = new Marca($this->db);
            $marcas = $marcaModel->obtenerTodos();
            
            header('Content-Type: application/json');
            echo json_encode($marcas);
            exit;
        }
    }
    
    // AJAX: Filtrar productos
    public function filtrar() {
        if ($this->isAjax()) {
            $filtros = [
                'idTipo' => $_POST['idTipo'] ?? null,
                'idMarca' => $_POST['idMarca'] ?? null,
                'precioMin' => $_POST['precioMin'] ?? null,
                'precioMax' => $_POST['precioMax'] ?? null,
                'sexo' => $_POST['sexo'] ?? null
            ];
            
            $productos = $this->productoModel->filtrar($filtros);
            
            header('Content-Type: application/json');
            echo json_encode($productos);
            exit;
        }
    }
    
    // AJAX: Buscar productos
    public function buscar() {
        if ($this->isAjax()) {
            $termino = $_POST['termino'] ?? '';
            
            if (empty($termino) || strlen($termino) < 2) {
                header('Content-Type: application/json');
                echo json_encode([]);
                exit;
            }
            
            $productos = $this->productoModel->buscar($termino);
            
            header('Content-Type: application/json');
            echo json_encode($productos);
            exit;
        }
    }
    
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>