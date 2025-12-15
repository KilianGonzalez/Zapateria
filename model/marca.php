<?php
class Marca {
    private $conn;
    private $table = "Marcas";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
