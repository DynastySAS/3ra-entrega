<?php

class Vivienda {
    private $conn;
    private $table_name = "vivienda";

    public $id_vivienda;
    public $calle;
    public $estado;
    public $nro_apt;
    public $nro_puerta;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        return $this->conn->query($query);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  (calle, estado, nro_apt, nro_puerta) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $stmt->bind_param(
            "ssii",
            $this->calle,
            $this->estado,
            $this->nro_apt,
            $this->nro_puerta
        );
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET calle=?, estado=?, nro_apt=?, nro_puerta=?
                  WHERE id_vivienda=?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) return false;

        $stmt->bind_param(
            "ssiii",
            $this->calle,
            $this->estado,
            $this->nro_apt,
            $this->nro_puerta,
            $this->id_vivienda
        );
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_vivienda=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id_vivienda);
        return $stmt->execute();
    }
}
