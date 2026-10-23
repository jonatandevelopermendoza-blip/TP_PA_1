<?php
require_once __DIR__ . '/Database.php';

class Libro {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listar() {
        $stmt = $this->db->query("SELECT * FROM libros ORDER BY titulo ASC");
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existeIsbn($isbn, $excluirId = null) {
        if (empty($isbn)) {
            return false;
        }
        if ($excluirId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM libros WHERE isbn = :isbn AND id != :id");
            $stmt->execute([':isbn' => $isbn, ':id' => $excluirId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM libros WHERE isbn = :isbn");
            $stmt->execute([':isbn' => $isbn]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }

    public function insertar($datos) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO libros (titulo, autor, isbn, categoria, anio_publicacion, stock_total, stock_disponible)
                 VALUES (:titulo, :autor, :isbn, :categoria, :anio, :stock_total, :stock_disponible)"
            );
            $stmt->execute([
                ':titulo'      => $datos['titulo'],
                ':autor'       => $datos['autor'],
                ':isbn'        => $datos['isbn'] ?: null,
                ':categoria'   => $datos['categoria'] ?: null,
                ':anio'        => $datos['anio_publicacion'] ?: null,
                ':stock_total' => (int)$datos['stock_total'],
                ':stock_disponible' => (int)$datos['stock_total'],
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
                    
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $libroActual = $this->obtenerPorId($id);
            if (!$libroActual) {
                return false;
            }

            $prestados = $libroActual['stock_total'] - $libroActual['stock_disponible'];
            $nuevoTotal = (int)$datos['stock_total'];

            if ($nuevoTotal < $prestados) {
                return 'stock_insuficiente';
            }

            $nuevoDisponible = $nuevoTotal - $prestados;

            $stmt = $this->db->prepare(
                "UPDATE libros
                 SET titulo = :titulo, autor = :autor, isbn = :isbn,
                     categoria = :categoria, anio_publicacion = :anio,
                     stock_total = :stock_total, stock_disponible = :stock_disponible
                 WHERE id = :id"
            );
            $stmt->execute([
                ':titulo'           => $datos['titulo'],
                ':autor'            => $datos['autor'],
                ':isbn'             => $datos['isbn'] ?: null,
                ':categoria'        => $datos['categoria'] ?: null,
                ':anio'             => $datos['anio_publicacion'] ?: null,
                ':stock_total'      => $nuevoTotal,
                ':stock_disponible' => $nuevoDisponible,
                ':id'               => $id,
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function tienePrestamosActivos($id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM prestamos WHERE libro_id = :id AND estado = 'activo'"
            );
            $stmt->execute([':id' => $id]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM libros WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>