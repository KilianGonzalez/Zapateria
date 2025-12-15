<?php
class Producto {
    private $conn;
    private $table = "Productos";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function obtenerTodos() {
        $query = "SELECT p.*, t.nombre as tipo, m.nombre as marca,
                  (SELECT ip.rutaImagen FROM ImagenesProducto ip 
                   WHERE ip.idProducto = p.id AND ip.esPrincipal = TRUE 
                   LIMIT 1) as imagenPrincipal
                  FROM " . $this->table . " p
                  LEFT JOIN tipoProductos t ON p.idTipo = t.id
                  LEFT JOIN Marcas m ON p.idMarca = m.id
                  ORDER BY p.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT p.*, t.nombre as tipo, m.nombre as marca 
                  FROM " . $this->table . " p
                  LEFT JOIN tipoProductos t ON p.idTipo = t.id
                  LEFT JOIN Marcas m ON p.idMarca = m.id
                  WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function obtenerPorTipo($idTipo) {
        $query = "SELECT p.*, t.nombre as tipo, m.nombre as marca,
                  (SELECT ip.rutaImagen FROM ImagenesProducto ip 
                   WHERE ip.idProducto = p.id AND ip.esPrincipal = TRUE 
                   LIMIT 1) as imagenPrincipal
                  FROM " . $this->table . " p
                  LEFT JOIN tipoProductos t ON p.idTipo = t.id
                  LEFT JOIN Marcas m ON p.idMarca = m.id
                  WHERE p.idTipo = :idTipo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTipo', $idTipo);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function filtrar($filtros) {
        $query = "SELECT p.*, t.nombre as tipo, m.nombre as marca,
                  (SELECT ip.rutaImagen FROM ImagenesProducto ip 
                   WHERE ip.idProducto = p.id AND ip.esPrincipal = TRUE 
                   LIMIT 1) as imagenPrincipal
                  FROM " . $this->table . " p
                  LEFT JOIN tipoProductos t ON p.idTipo = t.id
                  LEFT JOIN Marcas m ON p.idMarca = m.id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['idTipo'])) {
            $query .= " AND p.idTipo = :idTipo";
            $params[':idTipo'] = $filtros['idTipo'];
        }
        
        if (!empty($filtros['idMarca'])) {
            $query .= " AND p.idMarca = :idMarca";
            $params[':idMarca'] = $filtros['idMarca'];
        }
        
        if (!empty($filtros['precioMin'])) {
            $query .= " AND p.precio >= :precioMin";
            $params[':precioMin'] = $filtros['precioMin'];
        }
        
        if (!empty($filtros['precioMax'])) {
            $query .= " AND p.precio <= :precioMax";
            $params[':precioMax'] = $filtros['precioMax'];
        }
        
        if (!empty($filtros['sexo'])) {
            $query .= " AND p.sexo = :sexo";
            $params[':sexo'] = $filtros['sexo'];
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (idTipo, color, talla, precio, sexo, idMarca) 
                  VALUES (:idTipo, :color, :talla, :precio, :sexo, :idMarca)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':idTipo', $datos['idTipo']);
        $stmt->bindParam(':color', $datos['color']);
        $stmt->bindParam(':talla', $datos['talla']);
        $stmt->bindParam(':precio', $datos['precio']);
        $stmt->bindParam(':sexo', $datos['sexo']);
        $stmt->bindParam(':idMarca', $datos['idMarca']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function buscar($termino) {
    $query = "SELECT p.*, t.nombre as tipo, m.nombre as marca,
              (SELECT ip.rutaImagen FROM ImagenesProducto ip 
               WHERE ip.idProducto = p.id AND ip.esPrincipal = TRUE 
               LIMIT 1) as imagenPrincipal
              FROM " . $this->table . " p
              LEFT JOIN tipoProductos t ON p.idTipo = t.id
              LEFT JOIN Marcas m ON p.idMarca = m.id
              WHERE t.nombre LIKE :termino1
              OR m.nombre LIKE :termino2
              OR p.color LIKE :termino3
              OR p.talla LIKE :termino4
              ORDER BY p.id DESC
              LIMIT 20";
    
    $stmt = $this->conn->prepare($query);
    $terminoBusqueda = '%' . $termino . '%';
    $stmt->bindParam(':termino1', $terminoBusqueda);
    $stmt->bindParam(':termino2', $terminoBusqueda);
    $stmt->bindParam(':termino3', $terminoBusqueda);
    $stmt->bindParam(':termino4', $terminoBusqueda);
    $stmt->execute();
    return $stmt->fetchAll();
}
}
?>
