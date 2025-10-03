<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controladores/controladorUsuario.php";
require_once __DIR__ . "/../controladores/controladorPago.php";

$db = (new Database())->getConnection();

$usuarioController = new UsuarioController($db);
$pagoController = new PagoController($db);

$method = $_SERVER["REQUEST_METHOD"];
$path = explode("/", trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/"));
$action = $path[4] ?? null; 
$id = $path[5] ?? null;
$input = json_decode(file_get_contents("php://input"));

switch ($action) {
    case "usuarios":
        switch ($method) {
            case "GET":
                $id ? $usuarioController->show($id) : $usuarioController->index();
                break;
            case "PUT":
                if ($id) $usuarioController->aprobar($id);
                break;
            case "DELETE":
                if ($id) $usuarioController->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    case "pagos":
        switch ($method) {
            case "GET":
                $id ? $pagoController->show($id) : $pagoController->index();
                break;
            case "PUT":
                if ($id) $pagoController->aprobar($id);
                break;
            case "DELETE":
                if ($id) $pagoController->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Método no permitido"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Recurso no encontrado"]);
}
