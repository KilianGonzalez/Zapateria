<?php
require_once 'controllers/Database.php';
require_once 'models/Producto.php';
require_once 'models/ImagenProducto.php';
require_once 'models/TipoProducto.php';
require_once 'models/Marca.php';

class AdminController {
    private $db;
    private $productoModel;
    private $imagenModel;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productoModel = new Producto($this->db);
        $this->imagenModel = new ImagenProducto($this->db);
        
        // Verificar que el usuario está logueado y es admin
        if (!isset($_SESSION['usuario_id']) || $_SESSION['admin'] !== 't') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            header('Location: ' . BASE_URL . '/producto/index');
            exit;
        }
    }
    
    public function dashboard() {
        $productos = $this->productoModel->obtenerTodos();
        require_once 'views/admin/dashboard.php';
    }
    
    public function pedidos() {
        // Obtener todos los pedidos
        $query = "SELECT p.*, u.nom, u.correo, pr.color, pr.talla, pr.precio, 
                  t.nombre as tipo, m.nombre as marca
                  FROM Pedido p
                  INNER JOIN Usuarios u ON p.idCliente = u.id
                  INNER JOIN Productos pr ON p.idProducto = pr.id
                  LEFT JOIN tipoProductos t ON pr.idTipo = t.id
                  LEFT JOIN Marcas m ON pr.idMarca = m.id
                  ORDER BY p.id DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $pedidos = $stmt->fetchAll();
        
        require_once 'views/admin/pedidos.php';
    }
    
    public function crearProducto() {
        $tipoModel = new TipoProducto($this->db);
        $marcaModel = new Marca($this->db);
        
        $tipos = $tipoModel->obtenerTodos();
        $marcas = $marcaModel->obtenerTodos();
        
        require_once 'views/admin/producto_form.php';
    }
    
    public function guardarProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }
        
        $datos = [
            'idTipo' => $_POST['idTipo'] ?? '',
            'color' => trim($_POST['color'] ?? ''),
            'talla' => trim($_POST['talla'] ?? ''),
            'precio' => $_POST['precio'] ?? '',
            'sexo' => $_POST['sexo'] ?? '',
            'idMarca' => $_POST['idMarca'] ?? ''
        ];
        
        // Validación servidor
        if (empty($datos['idTipo']) || empty($datos['color']) || empty($datos['talla']) || 
            empty($datos['precio']) || empty($datos['sexo']) || empty($datos['idMarca'])) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: ' . BASE_URL . '/admin/crearProducto');
            exit;
        }
        
        if (!is_numeric($datos['precio']) || $datos['precio'] <= 0) {
            $_SESSION['error'] = 'El precio debe ser un número positivo';
            header('Location: ' . BASE_URL . '/admin/crearProducto');
            exit;
        }
        
        if (!in_array($datos['sexo'], ['M', 'F', 'U'])) {
            $_SESSION['error'] = 'Sexo no válido';
            header('Location: ' . BASE_URL . '/admin/crearProducto');
            exit;
        }
        
        $idProducto = $this->productoModel->crear($datos);
        
        if ($idProducto) {
            // Procesar imágenes
            $this->procesarImagenes($idProducto, $_FILES);
            
            $_SESSION['mensaje'] = 'Producto creado correctamente';
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Error al crear el producto';
            header('Location: ' . BASE_URL . '/admin/crearProducto');
            exit;
        }
    }
    
    public function editarProducto($id) {
    $producto = $this->productoModel->obtenerPorId($id);
    
    if (!$producto) {
        $_SESSION['error'] = 'Producto no encontrado';
        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    }
    
    $tipoModel = new TipoProducto($this->db);
    $marcaModel = new Marca($this->db);
    
    $tipos = $tipoModel->obtenerTodos();
    $marcas = $marcaModel->obtenerTodos();
    
    $imagenes = $this->imagenModel->obtenerPorProducto($id);
    
    require_once 'views/admin/producto_form.php';
}

public function actualizarProducto($id) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    }
    
    $datos = [
        'idTipo' => $_POST['idTipo'] ?? '',
        'color' => trim($_POST['color'] ?? ''),
        'talla' => trim($_POST['talla'] ?? ''),
        'precio' => $_POST['precio'] ?? '',
        'sexo' => $_POST['sexo'] ?? '',
        'idMarca' => $_POST['idMarca'] ?? ''
    ];
    
    // Validación servidor
    if (empty($datos['idTipo']) || empty($datos['color']) || empty($datos['talla']) || 
        empty($datos['precio']) || empty($datos['sexo']) || empty($datos['idMarca'])) {
        $_SESSION['error'] = 'Todos los campos son obligatorios';
        header('Location: ' . BASE_URL . '/admin/editarProducto/' . $id);
        exit;
    }
    
    if (!is_numeric($datos['precio']) || $datos['precio'] <= 0) {
        $_SESSION['error'] = 'El precio debe ser un número positivo';
        header('Location: ' . BASE_URL . '/admin/editarProducto/' . $id);
        exit;
    }
    
    if (!in_array($datos['sexo'], ['M', 'F', 'U'])) {
        $_SESSION['error'] = 'Sexo no válido';
        header('Location: ' . BASE_URL . '/admin/editarProducto/' . $id);
        exit;
    }
    
    if ($this->productoModel->actualizar($id, $datos)) {
        $_SESSION['mensaje'] = 'Producto actualizado correctamente';
        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    } else {
        $_SESSION['error'] = 'Error al actualizar el producto';
        header('Location: ' . BASE_URL . '/admin/editarProducto/' . $id);
        exit;
    }
}

    
    public function eliminarProducto($id) {
        $producto = $this->productoModel->obtenerPorId($id);
        
        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado';
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }
        
        // Eliminar imágenes físicas
        $imagenes = $this->imagenModel->obtenerPorProducto($id);
        foreach ($imagenes as $img) {
            $rutaArchivo = __DIR__ . '/../assets/uploads/productos/' . $img['rutaImagen'];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
        }
        
        // Eliminar imágenes de BD
        $this->imagenModel->eliminarPorProducto($id);
        
        // Eliminar producto
        if ($this->productoModel->eliminar($id)) {
            $_SESSION['mensaje'] = 'Producto eliminado correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el producto';
        }
        
        header('Location: ' . BASE_URL . '/admin/dashboard');
        exit;
    }
    
    private function procesarImagenes($idProducto, $files) {
        $carpetaDestino = __DIR__ . '/../assets/uploads/productos/';
        
        // Crear carpeta si no existe
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }
        
        $imagenesSubidas = 0;
        
        for ($i = 1; $i <= 4; $i++) {
            $nombreCampo = 'imagen' . $i;
            
            if (!empty($files[$nombreCampo]['name'])) {
                $archivo = $files[$nombreCampo];
                
                // Validar que es imagen
                $tipoArchivo = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    continue;
                }
                
                // Validar tamaño (máximo 5MB)
                if ($archivo['size'] > 5000000) {
                    continue;
                }
                
                // Generar nombre único
                $nombreArchivo = uniqid() . '_' . time() . '.' . $tipoArchivo;
                $rutaDestino = $carpetaDestino . $nombreArchivo;
                
                // Mover archivo
                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    // Guardar en BD
                    $esPrincipal = ($i === 1 && $imagenesSubidas === 0);
                    $this->imagenModel->crear($idProducto, $nombreArchivo, $i, $esPrincipal);
                    $imagenesSubidas++;
                }
            }
        }
        
        return $imagenesSubidas;
    }
}
?>