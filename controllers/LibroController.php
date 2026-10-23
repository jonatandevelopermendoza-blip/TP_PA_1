<?php

require_once __DIR__ . '/../funciones.php';
require_once __DIR__ . '/../models/Libro.php';

class LibroController {
    private $libroModel;
    private $esAdmin;
    private $esBiblio;
    private $puedeEditar;
    private $soloBiblio;

    public function __construct() {
        $this->libroModel = new Libro();
        $rol = $_SESSION['usuario_rol'] ?? '';

        $this->esAdmin    = ($rol === 'admin');
        $this->esBiblio   = ($rol === 'bibliotecario');
        $this->soloBiblio = $this->esBiblio;
    }

    public function manejarRequest() {
        $accion = $_GET['accion'] ?? 'listar';

        $accionesProtegidas = ['nuevo', 'guardar', 'editar', 'actualizar', 'eliminar'];
        if (in_array($accion, $accionesProtegidas) && !$this->soloBiblio) {
            setMensajeError('No tenés permiso para administrar el catálogo. Solo el Bibliotecario puede hacerlo.');
            header('Location: libros.php');
            exit();
        }

        switch ($accion) {
            case 'nuevo':      $this->nuevo();      break;
            case 'guardar':    $this->guardar();    break;
            case 'editar':     $this->editar();     break;
            case 'actualizar': $this->actualizar(); break;
            case 'eliminar':   $this->eliminar();   break;
            default:           $this->listar();     break;
        }
    }

    private function listar() {
        $libros = $this->libroModel->listar();

        $puedeEditar   = $this->soloBiblio;
        $mensajeExito  = getMensaje('exito');
        $mensajeError  = getMensaje('error');
        $titulo_pagina = 'Libros';
        $csrf_token    = generarTokenCSRF();

        require __DIR__ . '/../views/libros/listado.php';
    }

    private function nuevo() {
        $libro = [
            'id' => null,
            'titulo' => '',
            'autor' => '',
            'isbn' => '',
            'categoria' => '',
            'anio_publicacion' => '',
            'stock_total' => 1,
            'stock_disponible' => 1,
        ];
        $modoEdicion   = false;
        $errores       = [];
        $titulo_pagina = 'Nuevo Libro';
        $csrf_token    = generarTokenCSRF();

        require __DIR__ . '/../views/libros/formulario.php';
    }

    private function editar() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $libro = $this->libroModel->obtenerPorId($id);

        if (!$libro) {
            setMensajeError('El libro solicitado no existe.');
            header('Location: libros.php');
            exit();
        }

        $modoEdicion   = true;
        $errores       = [];
        $titulo_pagina = 'Editar Libro';
        $csrf_token    = generarTokenCSRF();

        require __DIR__ . '/../views/libros/formulario.php';
    }

    private function guardar() {
        $this->verificarPostYCsrf();

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validarDatos($datos, null);

        if (!empty($errores)) {
            $libro = array_merge(['id' => null, 'stock_disponible' => $datos['stock_total']], $datos);
            $modoEdicion   = false;
            $titulo_pagina = 'Nuevo Libro';
            $csrf_token    = generarTokenCSRF();
            require __DIR__ . '/../views/libros/formulario.php';
            return;
        }

        $resultado = $this->libroModel->insertar($datos);

        if ($resultado) {
            setMensajeExito('Libro creado correctamente.');
        } else {
            setMensajeError('No se pudo crear el libro. Intente nuevamente.');
        }
        header('Location: libros.php');
        exit();
    }

    private function actualizar() {
        $this->verificarPostYCsrf();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $libroExistente = $this->libroModel->obtenerPorId($id);

        if (!$libroExistente) {
            setMensajeError('El libro no existe.');
            header('Location: libros.php');
            exit();
        }

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validarDatos($datos, $id);

        if (!empty($errores)) {
            $libro = array_merge($libroExistente, $datos);
            $modoEdicion   = true;
            $titulo_pagina = 'Editar Libro';
            $csrf_token    = generarTokenCSRF();
            require __DIR__ . '/../views/libros/formulario.php';
            return;
        }

        $resultado = $this->libroModel->actualizar($id, $datos);

        if ($resultado === 'stock_insuficiente') {
            setMensajeError('No se puede reducir el stock por debajo de los ejemplares actualmente prestados.');
            header('Location: libros.php?accion=editar&id=' . $id);
            exit();
        }

        if ($resultado === true) {
            setMensajeExito('Libro actualizado correctamente.');
        } else {
            setMensajeError('No se pudo actualizar el libro.');
        }
        header('Location: libros.php');
        exit();
    }

    private function eliminar() {
        $this->verificarPostYCsrf();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $libro = $this->libroModel->obtenerPorId($id);

        if (!$libro) {
            setMensajeError('El libro no existe.');
            header('Location: libros.php');
            exit();
        }

        if ($this->libroModel->tienePrestamosActivos($id)) {
            setMensajeError('No se puede eliminar el libro porque tiene préstamos activos. Registrá primero la devolución.');
            header('Location: libros.php');
            exit();
        }

        if ($this->libroModel->eliminar($id)) {
            setMensajeExito('Libro eliminado correctamente.');
        } else {
            setMensajeError('No se pudo eliminar el libro.');
        }
        header('Location: libros.php');
        exit();
    }

    private function verificarPostYCsrf() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            setMensajeError('Método no permitido.');
            header('Location: libros.php');
            exit();
        }
        if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
            setMensajeError('Error de seguridad. Intente nuevamente.');
            header('Location: libros.php');
            exit();
        }
    }

    private function obtenerDatosFormulario() {
        return [
            'titulo'           => sanitizar($_POST['titulo'] ?? ''),
            'autor'            => sanitizar($_POST['autor'] ?? ''),
            'isbn'             => sanitizar($_POST['isbn'] ?? ''),
            'categoria'        => sanitizar($_POST['categoria'] ?? ''),
            'anio_publicacion' => isset($_POST['anio_publicacion']) && $_POST['anio_publicacion'] !== ''
                                    ? (int)$_POST['anio_publicacion'] : null,
            'stock_total'      => isset($_POST['stock_total']) ? (int)$_POST['stock_total'] : 0,
        ];
    }

    private function validarDatos($datos, $idExcluir) {
        $errores = [];

        if (empty($datos['titulo'])) {
            $errores['titulo'] = 'El título es obligatorio.';
        }
        if (empty($datos['autor'])) {
            $errores['autor'] = 'El autor es obligatorio.';
        }
        if ($datos['stock_total'] < 1) {
            $errores['stock_total'] = 'El stock debe ser al menos 1.';
        }
        if (!empty($datos['isbn']) && $this->libroModel->existeIsbn($datos['isbn'], $idExcluir)) {
            $errores['isbn'] = 'Ya existe un libro con ese ISBN.';
        }
        if (!empty($datos['anio_publicacion']) && ($datos['anio_publicacion'] < 1000 || $datos['anio_publicacion'] > (int)date('Y') + 1)) {
            $errores['anio_publicacion'] = 'El año no es válido.';
        }

        return $errores;
    }
}
?>