<?php
require_once __DIR__ . '/Database.php';

class Prestamo {
    private $db;
    private const MAX_PRESTAMOS_ACTIVOS = 3;
    private const DIAS_DOMICILIARIO    = 14;
    private const HORAS_SALA           = 4;
    private const UMBRAL_STOCK_BAJO    = 2;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarTodos() {
        $sql = "SELECT p.*, 
                       l.titulo AS libro_titulo, l.autor AS libro_autor,
                       u.nombre AS usuario_nombre, u.email AS usuario_email
                FROM prestamos p
                INNER JOIN libros l ON p.libro_id = l.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY p.estado ASC, p.fecha_prestamo DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function listarPorUsuario($usuario_id) {
        $sql = "SELECT p.*, 
                       l.titulo AS libro_titulo, l.autor AS libro_autor
                FROM prestamos p
                INNER JOIN libros l ON p.libro_id = l.id
                WHERE p.usuario_id = :uid
                ORDER BY 
                    CASE 
                        WHEN p.estado = 'activo' AND p.fecha_devolucion_esperada < NOW() THEN 1
                        WHEN p.estado = 'activo' THEN 2
                        ELSE 3
                    END ASC,
                    p.fecha_prestamo DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $usuario_id]);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $sql = "SELECT p.*, 
                       l.titulo AS libro_titulo, l.stock_disponible,
                       u.nombre AS usuario_nombre, u.rol AS usuario_rol
                FROM prestamos p
                INNER JOIN libros l ON p.libro_id = l.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function contarActivosPorUsuario($usuario_id) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM prestamos WHERE usuario_id = :uid AND estado = 'activo'"
        );
        $stmt->execute([':uid' => $usuario_id]);
        return (int)$stmt->fetchColumn();
    }

    public function usuarioTieneLibroActivo($usuario_id, $libro_id) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM prestamos 
             WHERE usuario_id = :uid AND libro_id = :lid AND estado = 'activo'"
        );
        $stmt->execute([':uid' => $usuario_id, ':lid' => $libro_id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function obtenerLibroParaPrestamo($libro_id) {
        $stmt = $this->db->prepare("SELECT id, titulo, stock_disponible FROM libros WHERE id = :id");
        $stmt->execute([':id' => $libro_id]);
        return $stmt->fetch();
    }

    public function calcularFechas($tipo) {
        $ahora = new DateTime();
        if ($tipo === 'sala') {
            $ahora->modify('+' . self::HORAS_SALA . ' hours');
        } else {
            $ahora->modify('+' . self::DIAS_DOMICILIARIO . ' days');
        }
        return [
            'prestamo'  => date('Y-m-d H:i:s'),
            'devolucion' => $ahora->format('Y-m-d H:i:s'),
        ];
    }

    public function crear($usuario_id, $libro_id, $tipo_solicitado) {
        try {
            $this->db->beginTransaction();

            // 1. Bloquear y verificar stock
            $stmt = $this->db->prepare("SELECT stock_disponible FROM libros WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $libro_id]);
            $libro = $stmt->fetch();

            if (!$libro) {
                $this->db->rollBack();
                return 'libro_no_existe';
            }
            if ((int)$libro['stock_disponible'] <= 0) {
                $this->db->rollBack();
                return 'sin_stock';
            }

            // 2. Verificar límite de préstamos activos del usuario
            if ($this->contarActivosPorUsuario($usuario_id) >= self::MAX_PRESTAMOS_ACTIVOS) {
                $this->db->rollBack();
                return 'limite_alcanzado';
            }

            // 3. Verificar duplicado del mismo libro
            if ($this->usuarioTieneLibroActivo($usuario_id, $libro_id)) {
                $this->db->rollBack();
                return 'libro_duplicado';
            }

            // 4. Forzar tipo sala si stock bajo
            $tipo = $tipo_solicitado;
            if ((int)$libro['stock_disponible'] <= self::UMBRAL_STOCK_BAJO) {
                $tipo = 'sala';
            }

            // 5. Calcular fechas
            $fechas = $this->calcularFechas($tipo);

            // 6. Insertar préstamo
            $stmt = $this->db->prepare(
                "INSERT INTO prestamos 
                 (libro_id, usuario_id, tipo, fecha_prestamo, fecha_devolucion_esperada, estado)
                 VALUES (:lid, :uid, :tipo, :fp, :fde, 'activo')"
            );
            $stmt->execute([
                ':lid'  => $libro_id,
                ':uid'  => $usuario_id,
                ':tipo' => $tipo,
                ':fp'   => $fechas['prestamo'],
                ':fde'  => $fechas['devolucion'],
            ]);

            // 7. Descontar stock
            $stmt = $this->db->prepare(
                "UPDATE libros SET stock_disponible = stock_disponible - 1 WHERE id = :id"
            );
            $stmt->execute([':id' => $libro_id]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function registrarDevolucion($id) {
        try {
            $this->db->beginTransaction();

            $prestamo = $this->obtenerPorId($id);

            if (!$prestamo) {
                $this->db->rollBack();
                return 'no_existe';
            }
            if ($prestamo['estado'] !== 'activo') {
                $this->db->rollBack();
                return 'ya_devuelto';
            }

            $stmt = $this->db->prepare(
                "UPDATE prestamos 
                 SET estado = 'devuelto', fecha_devolucion_real = NOW() 
                 WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);

            $stmt = $this->db->prepare(
                "UPDATE libros SET stock_disponible = stock_disponible + 1 WHERE id = :lid"
            );
            $stmt->execute([':lid' => $prestamo['libro_id']]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function esVencido($fecha_esperada) {
        return strtotime($fecha_esperada) < time();
    }

    public function contarPorEstado() {
        $stats = ['activo' => 0, 'devuelto' => 0];
        $stmt = $this->db->query("SELECT estado, COUNT(*) AS total FROM prestamos GROUP BY estado");
        foreach ($stmt->fetchAll() as $row) {
            $stats[$row['estado']] = (int)$row['total'];
        }
        return $stats;
    }
}
?>