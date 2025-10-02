<?php
// ===============================
// Configuración inicial
// ===============================
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ===============================
// Dependencias
// ===============================
require_once "../config/db.php";
require_once "../controladores/controladorUsuario.php";
require_once "../controladores/controladorPago.php";
require_once "../controladores/controladorTrabajo.php";

// ===============================
// Manejo de preflight (CORS)
// ===============================
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// ===============================
// Inicialización
// ===============================
$db     = (new Database())->getConnection();
$method = $_SERVER["REQUEST_METHOD"];
$action = $_GET["action"] ?? null;
$id     = $_GET["id"] ?? $_GET["id_usuario"] ?? null;
$input  = json_decode(file_get_contents("php://input"));
if (!$action && isset($_GET['id_usuario'])) {
    $action = 'usuario';
}

// ===============================
// Ruteo RESTful
// ===============================
switch ($action) {
    // ------------------ USUARIO ------------------
    case "usuario":
        $controller = new UsuarioController($db);

        if ($method === "GET") {
            $controller->show($id);
        } elseif ($method === "PUT") {
            $controller->update($input);
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Método no permitido en usuario"]);
        }
        break;

    // ------------------ PAGO ------------------
    case "pago":
        $controller = new PagoController($db);

        if ($method === "POST") {
            $controller->create($input);
        } elseif ($method === "GET") {
            $id ? $controller->show((int)$id) : $controller->index();
        } elseif ($method === "DELETE") {
            $controller->delete($input);
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Método no permitido en pago"]);
        }
        break;

    // ------------------ TRABAJO ------------------
    case "trabajo":
        $controller = new TrabajoController($db);

        if ($method === "POST") {
            $controller->store($input);
        } elseif ($method === "GET") {
            $controller->index();
        } elseif ($method === "PUT") {
            $controller->update($input);
        } elseif ($method === "DELETE") {
            $controller->delete($input);
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Método no permitido en trabajo"]);
        }
        break;

    // ------------------ DEFAULT ------------------
    default:
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Acción no reconocida"]);
}
