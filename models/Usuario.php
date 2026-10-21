<?php
require_once __DIR__ . '/Database.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function autenticar($email, $password) {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE (email = :login_email OR nombre = :login_nombre) AND activo = 1"
        );
        $stmt->execute([
            ':login_email'  => $email,
            ':login_nombre' => $email,
        ]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>