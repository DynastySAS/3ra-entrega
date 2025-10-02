<?php
class Pago {
    private $conn;
    private $table_name = "pago";

    public $id_pago;
    public $tipo_pago;
    public $monto;
    public $id_usuario;
    public $estado;
    public $fecha;
    public $fecha_aprobado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name} (tipo_pago, monto, id_usuario, estado)
                  VALUES (?, ?, ?, 'solicitado')";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $stmt->bind_param("sdi", $this->tipo_pago, $this->monto, $this->id_usuario);
        return $stmt->execute();
    }

    public function aprobar($id_pago) {
        $sql = "UPDATE {$this->table_name} 
                SET estado = 'aprobado', fecha_aprobado = CURRENT_TIMESTAMP 
                WHERE id_pago = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_pago);
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table_name}";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id_pago) {
        $query = "SELECT * FROM {$this->table_name} WHERE id_pago=? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_pago);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function delete($id_pago) {
        $query = "DELETE FROM {$this->table_name} WHERE id_pago=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_pago);
        return $stmt->execute();
    }
}
