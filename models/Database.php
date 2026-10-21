<?php
class Database {
    private static $host = 'localhost';
    private static $db_name = 'biblioteca_tp3';
    private static $username = 'root'; 
    private static $password = ''; 
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4",
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                // Validación de conexión: Mostrar mensaje amigable
                die("
                    <div style='font-family: Arial; text-align: center; margin-top: 50px;'>
                        <h2 style='color: #dc3545;'>Error de Conexión</h2>
                        <p>No pudimos conectar con el servicio en este momento.</p>
                        <p>Por favor, intentá nuevamente en unos minutos o contactá al administrador.</p>
                    </div>
                ");
            }
        }
        return self::$conn;
    }
}
?>