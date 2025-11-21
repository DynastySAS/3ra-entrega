<?php
class Database {
    private $host = "mysql";
    private $user = "sebastian.vazquez";
    private $pass = "56632237";
    private $db   = "cooperativa_viviendas";
    public $conn;

    public function getConnection() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";

            $this->conn = new PDO($dsn, $this->user, $this->pass);

            // Opciones recomendadas
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $this->conn;

        } catch (PDOException $e) {
            die("Error de conexión PDO: " . $e->getMessage());
        }
    }
}








