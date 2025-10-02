<?php 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../config/db.php";
require_once "../controladores/controladorUsuario.php";

// Preflight CORS
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// Conexión DB
$db = (new Database())->getConnection();
$controller = new UsuarioController($db);

// Método y parámetros
$method = $_SERVER["REQUEST_METHOD"];
$input  = json_decode(file_get_contents("php://input")); // 👈 usamos objeto
$id     = $_GET["id"] ?? null;
$action = $_GET["action"] ?? null;

// Ruteo RESTful
switch ($method) {
    case "GET":
        if ($id) {
            $controller->show((int)$id);
        } else {
            $controller->index();
        }
        break;

    case "POST":
        if ($action === "login") {
            $controller->login($input); // POST /usuario.php?action=login
        } else {
            $controller->store($input); // POST /usuario.php
        }
        break;

    case "PUT":
        if ($id) {
            $input->id_usuario = (int)$id;
            $controller->update($input); // PUT /usuario.php?id=123
        } elseif (!empty($input->id_usuario)) {
            $controller->update($input);
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Falta ID"]);
        }
        break;

    case "DELETE":
        if ($id) {
            $controller->delete((int)$id); // DELETE /usuario.php?id=123
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Falta ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
