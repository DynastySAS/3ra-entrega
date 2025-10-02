<?php
require_once __DIR__ . '/../modelos/Vivienda.php';

class ViviendaController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        $vivienda = new Vivienda($this->db);
        $result = $vivienda->read();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    }

    public function store($input) {
        $vivienda = new Vivienda($this->db);
        $vivienda->calle      = $input->calle;
        $vivienda->estado     = $input->estado;
        $vivienda->nro_apt    = $input->nro_apt;
        $vivienda->nro_puerta = $input->nro_puerta;

        echo json_encode(["success" => $vivienda->create()]);
    }

    public function update($input) {
        $vivienda = new Vivienda($this->db);
        $vivienda->id_vivienda = $input->id_vivienda;
        $vivienda->calle       = $input->calle;
        $vivienda->estado      = $input->estado;
        $vivienda->nro_apt     = $input->nro_apt;
        $vivienda->nro_puerta  = $input->nro_puerta;

        echo json_encode(["success" => $vivienda->update()]);
    }

    public function delete($input) {
        $vivienda = new Vivienda($this->db);
        $vivienda->id_vivienda = $input->id_vivienda;
        echo json_encode(["success" => $vivienda->delete()]);
    }
}
