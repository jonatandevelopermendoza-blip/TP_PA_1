<?php
require_once __DIR__ . '/Database.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ============================================
    // AUTENTICACIÓN (ya existía)
    // ============================================
    public function autenticar($login, $password) {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios 
             WHERE (email = :login_email OR nombre = :login_nombre) 
             AND activo = 1"
        );
        $stmt->execute([
            ':login_email'  => $login,
            ':login_nombre' => $login,
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // ============================================
    // CRUD
    // ============================================
    public function listar() {
        $sql = "SELECT u.*, 
                       (SELECT COUNT(*) FROM prestamos p WHERE p.usuario_id = u.id) AS tiene_prestamos
                FROM usuarios u
                ORDER BY u.nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existeEmail($email, $excluirId = null) {
        if (empty($email)) {
            return false;
        }
        if ($excluirId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id");
            $stmt->execute([':email' => $email, ':id' => $excluirId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    public function existeAdmin() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'");
        return (int)$stmt->fetchColumn() > 0;
    }

    public function esAdmin($id) {
        $stmt = $this->db->prepare("SELECT rol FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $rol = $stmt->fetchColumn();
        return $rol === 'admin';
    }

    public function insertar($datos) {
        try {
            // Regla: si ya existe un Admin, no se puede crear otro
            if ($datos['rol'] === 'admin' && $this->existeAdmin()) {
                return 'admin_existente';
            }

            $hash = password_hash($datos['password'], PASSWORD_BCRYPT);

            $stmt = $this->db->prepare(
                "INSERT INTO usuarios (nombre, email, password, rol, activo) 
                 VALUES (:nombre, :email, :password, :rol, :activo)"
            );
            $stmt->execute([
                ':nombre'   => $datos['nombre'],
                ':email'    => $datos['email'],
                ':password' => $hash,
                ':rol'      => $datos['rol'],
                ':activo'   => $datos['activo'] ? 1 : 0,
            ]);
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $usuarioActual = $this->obtenerPorId($id);
            if (!$usuarioActual) {
                return false;
            }

            // Regla: el Admin no puede cambiar de rol
            if ($usuarioActual['rol'] === 'admin' && $datos['rol'] !== 'admin') {
                return 'admin_no_degradable';
            }

            // Regla: el Admin no puede desactivarse
            if ($usuarioActual['rol'] === 'admin' && !$datos['activo']) {
                return 'admin_no_desactivable';
            }

            // Regla: no se puede crear un segundo Admin desde el form
            if ($datos['rol'] === 'admin' && $usuarioActual['rol'] !== 'admin' && $this->existeAdmin()) {
                return 'admin_existente';
            }

            // Si viene password nueva, la hasheamos; si no, mantenemos la actual
            if (!empty($datos['password'])) {
                $hash = password_hash($datos['password'], PASSWORD_BCRYPT);
                $stmt = $this->db->prepare(
                    "UPDATE usuarios 
                     SET nombre = :nombre, email = :email, password = :password, 
                         rol = :rol, activo = :activo
                     WHERE id = :id"
                );
                $stmt->execute([
                    ':nombre'   => $datos['nombre'],
                    ':email'    => $datos['email'],
                    ':password' => $hash,
                    ':rol'      => $datos['rol'],
                    ':activo'   => $datos['activo'] ? 1 : 0,
                    ':id'       => $id,
                ]);
            } else {
                $stmt = $this->db->prepare(
                    "UPDATE usuarios 
                     SET nombre = :nombre, email = :email, 
                         rol = :rol, activo = :activo
                     WHERE id = :id"
                );
                $stmt->execute([
                    ':nombre' => $datos['nombre'],
                    ':email'  => $datos['email'],
                    ':rol'    => $datos['rol'],
                    ':activo' => $datos['activo'] ? 1 : 0,
                    ':id'     => $id,
                ]);
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $usuario = $this->obtenerPorId($id);
            if (!$usuario) {
                return false;
            }

            // Regla: no se puede eliminar al Admin
            if ($usuario['rol'] === 'admin') {
                return 'admin_no_eliminable';
            }

            // Regla: no se puede eliminar si tiene préstamos (históricos o activos)
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM prestamos WHERE usuario_id = :id");
            $stmt->execute([':id' => $id]);
            if ((int)$stmt->fetchColumn() > 0) {
                return 'tiene_prestamos';
            }

            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>