<?php
class Usuario {
    private $conn;
    private $table = "usuario";

    public $id_usuario;
    public $id_persona;
    public $nombre;
    public $apellido;
    public $email_cont;
    public $telefono_cont;
    public $estado;
    public $usuario_login;
    public $rol;
    public $contrasena;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los usuarios
    public function getAll() {
    $sql = "SELECT id_usuario, id_persona, nombre, apellido, email_cont, telefono_cont, 
                   usuario_login, estado, rol 
            FROM $this->table";
    $result = $this->conn->query($sql);
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    return $usuarios; 
}


    // Obtener usuario por ID
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT id_usuario, id_persona, nombre, apellido, email_cont, telefono_cont, 
                    usuario_login, estado, rol 
             FROM $this->table WHERE id_usuario=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Crear usuario
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO $this->table 
             (id_persona, nombre, apellido, email_cont, usuario_login, contrasena, estado, rol)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $hash = password_hash($data->password, PASSWORD_BCRYPT);
        $estado_default = 'solicitado';
        $rol_default = $data->rol ?? 'cooperativista';

        $stmt->bind_param(
            "isssssss",
            $data->id_persona,
            $data->nombre,
            $data->apellido,
            $data->email_cont,
            $data->usuario_login,
            $hash,
            $estado_default,
            $rol_default
        );

        return $stmt->execute();
    }

    // Login
    public function login($identificador, $password) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM $this->table WHERE usuario_login=? OR email_cont=?"
        );
        $stmt->bind_param("ss", $identificador, $identificador);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            if ($usuario['estado'] !== 'registrado') {
                return false; // Usuario aún no aprobado
            }
            unset($usuario['contrasena']);
            return $usuario;
        }
        return false;
    }

    // Actualizar usuario (admin/backoffice)
    public function update($data) {
    $stmt = $this->conn->prepare(
        "UPDATE $this->table 
         SET email_cont=?, telefono_cont=?, usuario_login=? 
         WHERE id_usuario=?"
    );

    $email    = $data->email_cont ?? '';
    $telefono = $data->telefono_cont ?? '';
    $usuario  = $data->usuario_login ?? '';
    $id       = $data->id_usuario;

    $stmt->bind_param("sssi", $email, $telefono, $usuario, $id);

    if (!$stmt->execute()) {
        error_log("Error update usuario: " . $stmt->error);
        return false;
    }
    return true;
}

    // Aprobar usuario
    public function aprobar($id) {
            $sql = "UPDATE usuario SET estado = 'registrado' WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
    
public function getPendientes() {
    $sql = "SELECT id_usuario, id_persona, nombre, apellido, email_cont, telefono_cont, 
                   usuario_login, estado, rol 
            FROM $this->table
            WHERE estado = 'solicitado'";
    $result = $this->conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}   
    
    // Borrar usuario
public function delete($id) {
    $sql = "DELETE FROM usuario WHERE id_usuario = ?";
    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        error_log("Error prepare delete: " . $this->conn->error);
        return false;
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        error_log("Error execute delete: " . $stmt->error);
        return false;
    }

    return $stmt->affected_rows > 0;
}


}
