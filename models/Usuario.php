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
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
    
    public function verificarCorreoExiste($correo) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
    
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, cognom, telefono, correo, contraseña, admin, preguntaSeguridad, respuestaSeguridad) 
                  VALUES (:nom, :cognom, :telefono, :correo, :contrasena, :admin, :preguntaSeguridad, :respuestaSeguridad)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $datos['nom']);
        $stmt->bindParam(':cognom', $datos['cognom']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':contrasena', $datos['contrasena']);
        $stmt->bindParam(':admin', $datos['admin']);
        $stmt->bindParam(':preguntaSeguridad', $datos['preguntaSeguridad']);
        $stmt->bindParam(':respuestaSeguridad', $datos['respuestaSeguridad']);
        
        return $stmt->execute();
    }
    
    public function actualizar($id, $datos) {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, cognom = :cognom, telefono = :telefono, correo = :correo
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $datos['nom']);
        $stmt->bindParam(':cognom', $datos['cognom']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':correo', $datos['correo']);
        
        return $stmt->execute();
    }
    
    public function actualizarContrasena($id, $nuevaContrasena) {
        $query = "UPDATE " . $this->table . " SET contraseña = :contrasena WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':contrasena', $nuevaContrasena);
        return $stmt->execute();
    }
    
    public function verificarRespuestaSeguridad($correo, $pregunta, $respuesta) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE correo = :correo 
                  AND preguntaSeguridad = :pregunta 
                  AND respuestaSeguridad = :respuesta";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':pregunta', $pregunta);
        $stmt->bindParam(':respuesta', $respuesta);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>