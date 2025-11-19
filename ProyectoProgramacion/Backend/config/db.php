<?php
class Database {
    private $host = "192.168.5.50";
    private $user = "sebastian.vazquez";
    private $pass = "56632237";
    private $db   = "MiTecho-BD";
    public $conn;

    public function getConnection() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
        return $this->conn;
    }
}



