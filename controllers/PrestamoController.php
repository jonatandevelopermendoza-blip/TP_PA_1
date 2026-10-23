<?php
require_once __DIR__ . '/../funciones.php';
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/../models/Usuario.php';

class PrestamoController {
    private $prestamoModel;
    private $libroModel;
    private $usuarioModel;
    private $esBiblio;
    private $esUsuario;
    private $idLogueado;

    public function __construct() {
        $this->prestamoModel = new Prestamo();
        $this->libroModel    = new Libro();
        $this->usuarioModel  = new Usuario();
        $rol = $_SESSION['usuario_rol'] ?? '';
        $this->esBiblio  = ($rol === 'bibliotecario');
        $this->esUsuario = ($rol === 'usuario');
        $this->idLogueado = (int)($_SESSION['usuario_id'] ?? 0);
    }

    public function manejarRequest() {
        if (!$this->esBiblio) {
            setMensajeError('No tenés permiso para acceder a esta sección.');
            header('Location: inicio.php');
            exit();
        }

        $accion = $_GET['accion'] ?? 'listar';

        switch ($accion) {
            case 'nuevo':     $this->nuevo();     break;
            case 'guardar':   $this->guardar();   break;
            case 'devolver':  $this->devolver();  break;
            default:          $this->listar();    break;
        }
    }

    public function manejarMisPrestamos() {
        if (!$this->esUsuario) {
            setMensajeError('Solo los usuarios pueden acceder a "Mis Préstamos".');
            header('Location: inicio.php');
            exit();
        }

        $prestamos = $this->prestamoModel->listarPorUsuario($this->idLogueado);
        $titulo_pagina = 'Mis Préstamos';

        require __DIR__ . '/../views/prestamos/mis-prestamos.php';
    }

    private function listar() {
        $prestamos = $this->prestamoModel->listarTodos();
        $mensajeExito = getMensaje('exito');
        $mensajeError = getMensaje('error');
        $titulo_pagina = 'Préstamos';
        $csrf_token = generarTokenCSRF();

        require __DIR__ . '/../views/prestamos/listado.php';
    }

    private function nuevo() {
        $usuarios = $this->usuarioModel->listar();
        // Filtramos: solo activos y no admin
        $usuarios = array_filter($usuarios, function($u) {
            return (int)$u['activo'] === 1 && $u['rol'] !== 'admin';
        });

        $libros = $this->libroModel->listar();
        // Filtramos: solo con stock > 0
        $libros = array_filter($libros, function($l) {
            return (int)$l['stock_disponible'] > 0;
        });

        $errores = [];
        $datos = ['usuario_id' => '', 'libro_id' => '', 'tipo' => 'domiciliario'];
        $titulo_pagina = 'Nuevo Préstamo';
        $csrf_token = generarTokenCSRF();

        require __DIR__ . '/../views/prestamos/formulario.php';
    }

    private function guardar() {
        $this->verificarPostYCsrf();

        $usuario_id = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
        $libro_id   = isset($_POST['libro_id'])   ? (int)$_POST['libro_id']   : 0;
        $tipo       = sanitizar($_POST['tipo'] ?? 'domiciliario');

        if (!in_array($tipo, ['domiciliario', 'sala'])) {
            $tipo = 'domiciliario';
        }

        $errores = [];
        if ($usuario_id <= 0) $errores['usuario_id'] = 'Seleccioná un usuario.';
        if ($libro_id <= 0)   $errores['libro_id']   = 'Seleccioná un libro.';

        // Validar usuario
        $usuario = null;
        if (empty($errores['usuario_id'])) {
            $usuario = $this->usuarioModel->obtenerPorId($usuario_id);
            if (!$usuario || (int)$usuario['activo'] !== 1 || $usuario['rol'] === 'admin') {
                $errores['usuario_id'] = 'El usuario no es válido.';
            }
        }

        // Validar libro
        $libro = null;
        if (empty($errores['libro_id'])) {
            $libro = $this->prestamoModel->obtenerLibroParaPrestamo($libro_id);
            if (!$libro) {
                $errores['libro_id'] = 'El libro no existe.';
            } elseif ((int)$libro['stock_disponible'] <= 0) {
                $errores['libro_id'] = 'No hay ejemplares disponibles.';
            }
        }

        if (!empty($errores)) {
            $usuarios = $this->usuarioModel->listar();
            $usuarios = array_filter($usuarios, fn($u) => (int)$u['activo'] === 1 && $u['rol'] !== 'admin');
            $libros = $this->libroModel->listar();
            $libros = array_filter($libros, fn($l) => (int)$l['stock_disponible'] > 0);
            $datos = ['usuario_id' => $usuario_id, 'libro_id' => $libro_id, 'tipo' => $tipo];
            $titulo_pagina = 'Nuevo Préstamo';
            $csrf_token = generarTokenCSRF();
            require __DIR__ . '/../views/prestamos/formulario.php';
            return;
        }

        $resultado = $this->prestamoModel->crear($usuario_id, $libro_id, $tipo);

        $mensajes = [
            'sin_stock'        => 'No hay ejemplares disponibles de ese libro.',
            'limite_alcanzado' => 'El usuario ya tiene 3 préstamos activos (límite máximo).',
            'libro_duplicado'  => 'El usuario ya tiene un ejemplar de ese libro en préstamo.',
            'libro_no_existe'  => 'El libro ya no existe.',
        ];

        if ($resultado === true) {
            setMensajeExito('Préstamo registrado correctamente.');
        } elseif (is_string($resultado) && isset($mensajes[$resultado])) {
            setMensajeError($mensajes[$resultado]);
        } else {
            setMensajeError('No se pudo registrar el préstamo.');
        }

        header('Location: prestamos.php');
        exit();
    }

    private function devolver() {
        $this->verificarPostYCsrf();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        $resultado = $this->prestamoModel->registrarDevolucion($id);

        if ($resultado === true) {
            setMensajeExito('Devolución registrada correctamente.');
        } elseif ($resultado === 'ya_devuelto') {
            setMensajeError('Este préstamo ya fue devuelto.');
        } elseif ($resultado === 'no_existe') {
            setMensajeError('El préstamo no existe.');
        } else {
            setMensajeError('No se pudo registrar la devolución.');
        }

        header('Location: prestamos.php');
        exit();
    }

    private function verificarPostYCsrf() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            setMensajeError('Método no permitido.');
            header('Location: prestamos.php');
            exit();
        }
        if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
            setMensajeError('Error de seguridad.');
            header('Location: prestamos.php');
            exit();
        }
    }
}
?>