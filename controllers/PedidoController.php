<?php
require_once 'controllers/Database.php';
require_once 'models/Pedido.php';
require_once 'models/Producto.php';
require_once 'models/ImagenProducto.php';

class PedidoController {
    private $db;
    private $pedidoModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pedidoModel = new Pedido($this->db);
    }
    
    public function carrito() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        $carrito = $_SESSION['carrito'] ?? [];
        $productos = [];
        $total = 0;
        
        if (!empty($carrito)) {
            $productoModel = new Producto($this->db);
            $imagenModel = new ImagenProducto($this->db);
            
            foreach ($carrito as $idProducto => $cantidad) {
                $producto = $productoModel->obtenerPorId($idProducto);
                if ($producto) {
                    // Obtener imagen principal
                    $imagenes = $imagenModel->obtenerPorProducto($idProducto);
                    $imagenPrincipal = 'default.jpg';
                    
                    if (!empty($imagenes)) {
                        foreach ($imagenes as $img) {
                            if ($img['esPrincipal']) {
                                $imagenPrincipal = $img['rutaImagen'];
                                break;
                            }
                        }
                        // Si no hay principal, usar la primera
                        if ($imagenPrincipal == 'default.jpg' && isset($imagenes[0])) {
                            $imagenPrincipal = $imagenes[0]['rutaImagen'];
                        }
                    }
                    
                    $producto['imagenPrincipal'] = $imagenPrincipal;
                    $producto['cantidad'] = $cantidad;
                    $producto['subtotal'] = $producto['precio'] * $cantidad;
                    $productos[] = $producto;
                    $total += $producto['subtotal'];
                }
            }
        }
        
        require_once 'views/pedido/carrito.php';
    }
    
    // AJAX: Agregar al carrito
    public function agregarCarrito() {
        if ($this->isAjax()) {
            if (!isset($_SESSION['usuario_id'])) {
                echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
                exit;
            }
            
            $idProducto = $_POST['idProducto'] ?? null;
            $cantidad = $_POST['cantidad'] ?? 1;
            
            if (!$idProducto) {
                echo json_encode(['success' => false, 'message' => 'Producto no válido']);
                exit;
            }
            
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }
            
            if (isset($_SESSION['carrito'][$idProducto])) {
                $_SESSION['carrito'][$idProducto] += $cantidad;
            } else {
                $_SESSION['carrito'][$idProducto] = $cantidad;
            }
            
            $totalItems = array_sum($_SESSION['carrito']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Producto agregado al carrito',
                'totalItems' => $totalItems
            ]);
            exit;
        }
    }
    
    // AJAX: Actualizar cantidad
    public function actualizarCantidad() {
        if ($this->isAjax()) {
            $idProducto = $_POST['idProducto'] ?? null;
            $cantidad = (int)($_POST['cantidad'] ?? 0);
            
            if (!$idProducto || $cantidad < 0) {
                echo json_encode(['success' => false, 'message' => 'Datos no válidos']);
                exit;
            }
            
            if ($cantidad === 0) {
                unset($_SESSION['carrito'][$idProducto]);
            } else {
                $_SESSION['carrito'][$idProducto] = $cantidad;
            }
            
            $productoModel = new Producto($this->db);
            $producto = $productoModel->obtenerPorId($idProducto);
            $subtotal = $producto['precio'] * $cantidad;
            
            $total = 0;
            foreach ($_SESSION['carrito'] as $id => $cant) {
                $prod = $productoModel->obtenerPorId($id);
                $total += $prod['precio'] * $cant;
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'subtotal' => number_format($subtotal, 2),
                'total' => number_format($total, 2)
            ]);
            exit;
        }
    }
    
    // AJAX: Eliminar del carrito
    public function eliminarDelCarrito() {
        if ($this->isAjax()) {
            $idProducto = $_POST['idProducto'] ?? null;
            
            if (!$idProducto) {
                echo json_encode(['success' => false, 'message' => 'Producto no válido']);
                exit;
            }
            
            unset($_SESSION['carrito'][$idProducto]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
            exit;
        }
    }
    
    public function checkout() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        if (empty($_SESSION['carrito'])) {
            header('Location: ' . BASE_URL . '/pedido/carrito');
            exit;
        }
        
        require_once 'views/pedido/checkout.php';
    }
    
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/pedido/carrito');
            exit;
        }
        
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
            header('Location: ' . BASE_URL . '/pedido/carrito');
            exit;
        }
        
        $idCliente = $_SESSION['usuario_id'];
        $direccion = trim($_POST['direccion'] ?? '');
        $cuentaBancaria = trim($_POST['cuentaBancaria'] ?? '');
        
        // Validación servidor
        if (empty($direccion) || empty($cuentaBancaria)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: ' . BASE_URL . '/pedido/checkout');
            exit;
        }
        
        if (!preg_match('/^ES\d{22}$/', $cuentaBancaria)) {
            $_SESSION['error'] = 'Formato de cuenta bancaria no válido (debe ser ES seguido de 22 dígitos)';
            header('Location: ' . BASE_URL . '/pedido/checkout');
            exit;
        }
        
        $productoModel = new Producto($this->db);
        $precioTotal = 0;
        
        foreach ($_SESSION['carrito'] as $idProducto => $cantidad) {
            $producto = $productoModel->obtenerPorId($idProducto);
            $precioTotal += $producto['precio'] * $cantidad;
        }
        
        $datosPedido = [
            'idCliente' => $idCliente,
            'direccion' => $direccion,
            'cuentaBancaria' => $cuentaBancaria,
            'precioTotal' => $precioTotal
        ];
        
        $idPedido = $this->pedidoModel->crear($datosPedido, $_SESSION['carrito']);
        
        if ($idPedido) {
            unset($_SESSION['carrito']);
            $_SESSION['mensaje'] = 'Pedido realizado correctamente';
            header('Location: ' . BASE_URL . '/usuario/perfil');
            exit;
        } else {
            $_SESSION['error'] = 'Error al procesar el pedido';
            header('Location: ' . BASE_URL . '/pedido/checkout');
            exit;
        }
    }
    
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>