<?php
require_once __DIR__ . '/../modelos/Pago.php';

class PagoController {
    private $db;
    public function __construct($db) { $this->db = $db; }

    // POST /cooperativa.php?action=pago
    public function create($input) {
        $pago = new Pago($this->db);
        $pago->tipo_pago = $input->tipo_pago ?? '';
        $pago->monto     = $input->monto ?? 0;
        $pago->id_usuario = $input->id_usuario ?? null;

        $ok = $pago->create();
        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Pago registrado, pendiente de aprobación" : "Error al registrar pago"
        ]);
    }

    // GET 
    public function index() {
        $pago = new Pago($this->db);
        $data = $pago->getAll();
        echo json_encode(["success" => true, "data" => $data]);
    }

    // GET 
    public function show($id) {
        $pago = new Pago($this->db);
        $data = $pago->getById($id);
        if ($data) {
            echo json_encode(["success" => true, "data" => $data]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Pago no encontrado"]);
        }
    }

    // PUT 
    public function aprobar($id) {
        $pago = new Pago($this->db);
        $ok = $pago->aprobar($id);
        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Pago aprobado" : "Error al aprobar pago"
        ]);
    }

    // DELETE /cooperativa.php?action=pago
    public function delete($input) {
        if (empty($input->id_pago)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Falta ID"]);
            return;
        }

        $pago = new Pago($this->db);
        $ok = $pago->delete($input->id_pago);
        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Pago eliminado" : "Error al eliminar pago"
        ]);
    }
}
