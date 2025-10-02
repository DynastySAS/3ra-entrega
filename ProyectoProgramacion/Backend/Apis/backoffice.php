<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controladores/controladorUsuario.php";
require_once __DIR__ . "/../controladores/controladorPago.php";

$db = (new Database())->getConnection();

$method = $_SERVER["REQUEST_METHOD"];
$action = $_GET["action"] ?? null;
$id = $_GET["id"] ?? null;
$input = json_decode(file_get_contents("php://input"));

$usuarioController = new UsuarioController($db);
$pagoController = new PagoController($db);

switch ($action) {
    case "usuario":
        if ($method === "GET") {
            $id ? $usuarioController->show($id) : $usuarioController->index();
        } elseif ($method === "PUT" && $id) {
            $usuarioController->aprobar($id);
        } elseif ($method === "DELETE" && $id) {
            $usuarioController->delete($id);
        }else {
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case "pago":
    if ($method === "GET") {
        $id ? $pagoController->show($id) : $pagoController->index();
    } elseif ($method === "PUT" && $id) {
        $pagoController->aprobar($id);
    } else {
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
    }
    break;

    default:
        http_response_code(400);
        echo json_encode(["message" => "Acción no válida"]);
        break;
}
