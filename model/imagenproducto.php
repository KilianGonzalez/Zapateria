<?php
class ImagenProducto {
    private $conn;
    private $table = "ImagenesProducto";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function obtenerPorProducto($idProducto) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE idProducto = :idProducto 
                  ORDER BY orden ASC, esPrincipal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idProducto', $idProducto);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function crear($idProducto, $rutaImagen, $orden = 1, $esPrincipal = false) {
        $query = "INSERT INTO " . $this->table . " 
                  (idProducto, rutaImagen, orden, esPrincipal) 
                  VALUES (:idProducto, :rutaImagen, :orden, :esPrincipal)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idProducto', $idProducto);
        $stmt->bindParam(':rutaImagen', $rutaImagen);
        $stmt->bindParam(':orden', $orden);
        $stmt->bindParam(':esPrincipal', $esPrincipal, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
}
?>
