<?php
require_once 'controllers/Database.php';
require_once 'models/Pedido.php';
require_once 'models/Producto.php';

class PedidoController {
    private $db;
    private $pedidoModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pedidoModel = new Pedido($this->db);
    }
    
    // Ver carrito
    public function carrito() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        $carrito = $_SESSION['carrito'] ?? [];
        $productos = [];
        $total = 0;
        
        if (!empty($carrito)) {
            $productoModel = new Producto($this->db);
            foreach ($carrito as $idProducto => $cantidad) {
                $producto = $productoModel->obtenerPorId($idProducto);
                if ($producto) {
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
            
            // Inicializar carrito si no existe
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }
            
            // Agregar o incrementar cantidad
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
    
    // AJAX: Actualizar cantidad en carrito
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
            
            // Calcular nuevo total
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
    
    // Finalizar compra
    public function checkout() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        if (empty($_SESSION['carrito'])) {
            header('Location: /pedido/carrito');
            exit;
        }
        
        require_once 'views/pedido/checkout.php';
    }
    
    // Procesar pedido
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pedido/carrito');
            exit;
        }
        
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
            header('Location: /pedido/carrito');
            exit;
        }
        
        $idCliente = $_SESSION['usuario_id'];
        $direccion = $_POST['direccion'] ?? '';
        $cuentaBancaria = $_POST['cuentaBancaria'] ?? '';
        
        // Calcular precio total
        $productoModel = new Producto($this->db);
        $precioTotal = 0;
        
        foreach ($_SESSION['carrito'] as $idProducto => $cantidad) {
            $producto = $productoModel->obtenerPorId($idProducto);
            $precioTotal += $producto['precio'] * $cantidad;
        }
        
        // Crear pedido
        $datosPedido = [
            'idCliente' => $idCliente,
            'direccion' => $direccion,
            'cuentaBancaria' => $cuentaBancaria,
            'precioTotal' => $precioTotal
        ];
        
        $idPedido = $this->pedidoModel->crear($datosPedido, $_SESSION['carrito']);
        
        if ($idPedido) {
            // Limpiar carrito
            unset($_SESSION['carrito']);
            
            $_SESSION['mensaje'] = 'Pedido realizado correctamente';
            header('Location: /usuario/perfil');
            exit;
        } else {
            $_SESSION['error'] = 'Error al procesar el pedido';
            header('Location: /pedido/checkout');
            exit;
        }
    }
    
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>