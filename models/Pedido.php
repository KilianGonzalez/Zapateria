<?php
class Pedido {
    private $conn;
    private $table = "Pedido";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function crear($datos, $productos) {
        try {
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO " . $this->table . " 
                      (idCliente, idProducto, direccion, cuentaBancaria, precioTotal) 
                      VALUES (:idCliente, :idProducto, :direccion, :cuentaBancaria, :precioTotal)";
            
            $idPedido = null;
            
            foreach ($productos as $idProducto => $cantidad) {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':idCliente', $datos['idCliente'], PDO::PARAM_INT);
                $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
                $stmt->bindParam(':direccion', $datos['direccion']);
                $stmt->bindParam(':cuentaBancaria', $datos['cuentaBancaria']);
                
                $queryPrecio = "SELECT precio FROM Productos WHERE id = :id";
                $stmtPrecio = $this->conn->prepare($queryPrecio);
                $stmtPrecio->bindParam(':id', $idProducto, PDO::PARAM_INT);
                $stmtPrecio->execute();
                $producto = $stmtPrecio->fetch();
                $precioProducto = $producto['precio'] * $cantidad;
                
                $stmt->bindParam(':precioTotal', $precioProducto);
                $stmt->execute();
                
                if (!$idPedido) {
                    $idPedido = $this->conn->lastInsertId();
                }
            }
            
            $this->conn->commit();
            return $idPedido;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function obtenerPorUsuario($idCliente) {
        $query = "SELECT p.*, pr.color, pr.talla, pr.precio, t.nombre as tipo, m.nombre as marca
                  FROM " . $this->table . " p
                  INNER JOIN Productos pr ON p.idProducto = pr.id
                  LEFT JOIN tipoProductos t ON pr.idTipo = t.id
                  LEFT JOIN Marcas m ON pr.idMarca = m.id
                  WHERE p.idCliente = :idCliente
                  ORDER BY p.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idCliente', $idCliente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>