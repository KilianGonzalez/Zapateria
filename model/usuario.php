<?php
class Usuario {
    private $conn;
    private $table = "Usuarios";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function obtenerPorCorreo($correo) {
        $query = "SELECT * FROM " . $this->table . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, cognom, telefono, correo, contrasena, admin) 
                  VALUES (:nom, :cognom, :telefono, :correo, :contrasena, :admin)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $datos['nom']);
        $stmt->bindParam(':cognom', $datos['cognom']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':contrasena', $datos['contrasena']);
        $stmt->bindParam(':admin', $datos['admin']);
        
        return $stmt->execute();
    }
    
    public function actualizar($id, $datos) {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, cognom = :cognom, telefono = :telefono, correo = :correo
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $datos['nom']);
        $stmt->bindParam(':cognom', $datos['cognom']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':correo', $datos['correo']);
        
        return $stmt->execute();
    }
}
?>
