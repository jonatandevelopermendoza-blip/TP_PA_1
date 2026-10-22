<?php
require_once __DIR__ . '/../funciones.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $usuarioModel;
    private $esAdmin;
    private $idLogueado;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->esAdmin      = ($_SESSION['usuario_rol'] ?? '') === 'admin';
        $this->idLogueado   = (int)($_SESSION['usuario_id'] ?? 0);
    }

    public function manejarRequest() {
        if (!$this->esAdmin) {
            setMensajeError('No tenés permiso para acceder a la gestión de usuarios.');
            header('Location: inicio.php');
            exit();
        }

        $accion = $_GET['accion'] ?? 'listar';

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
        $usuarios      = $this->usuarioModel->listar();
        $idLogueado    = $this->idLogueado;
        $mensajeExito  = getMensaje('exito');
        $mensajeError  = getMensaje('error');
        $titulo_pagina = 'Usuarios';
        $csrf_token    = generarTokenCSRF();

        require __DIR__ . '/../views/usuarios/listado.php';
    }

    private function nuevo() {
        $usuario = [
            'id'       => null,
            'nombre'   => '',
            'email'    => '',
            'rol'      => 'usuario',
            'activo'   => 1,
        ];
        $modoEdicion   = false;
        $esAdminTarget = false;
        $errores       = [];
        $titulo_pagina = 'Nuevo Usuario';
        $csrf_token    = generarTokenCSRF();

        require __DIR__ . '/../views/usuarios/formulario.php';
    }

    private function editar() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {
            setMensajeError('El usuario solicitado no existe.');
            header('Location: usuarios.php');
            exit();
        }

        $modoEdicion   = true;
        $esAdminTarget = ($usuario['rol'] === 'admin');
        $errores       = [];
        $titulo_pagina = 'Editar Usuario';
        $csrf_token    = generarTokenCSRF();

        require __DIR__ . '/../views/usuarios/formulario.php';
    }

    private function guardar() {
        $this->verificarPostYCsrf();

        $datos   = $this->obtenerDatosFormulario();
        $errores = $this->validarDatos($datos, null);

        if (!empty($errores)) {
            $usuario       = array_merge(['id' => null, 'activo' => 1], $datos);
            $modoEdicion   = false;
            $esAdminTarget = ($datos['rol'] === 'admin');
            $titulo_pagina = 'Nuevo Usuario';
            $csrf_token    = generarTokenCSRF();
            require __DIR__ . '/../views/usuarios/formulario.php';
            return;
        }

        $resultado = $this->usuarioModel->insertar($datos);

        if ($resultado === 'admin_existente') {
            setMensajeError('Ya existe un administrador en el sistema. No se puede crear otro.');
            header('Location: usuarios.php');
            exit();
        }

        if ($resultado) {
            setMensajeExito('Usuario creado correctamente.');
        } else {
            setMensajeError('No se pudo crear el usuario. Intente nuevamente.');
        }
        header('Location: usuarios.php');
        exit();
    }

    private function actualizar() {
        $this->verificarPostYCsrf();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $usuarioExistente = $this->usuarioModel->obtenerPorId($id);

        if (!$usuarioExistente) {
            setMensajeError('El usuario no existe.');
            header('Location: usuarios.php');
            exit();
        }

        $datos   = $this->obtenerDatosFormulario();
        $errores = $this->validarDatos($datos, $id);

        if (!empty($errores)) {
            $usuario       = array_merge($usuarioExistente, $datos);
            $modoEdicion   = true;
            $esAdminTarget = ($usuarioExistente['rol'] === 'admin');
            $titulo_pagina = 'Editar Usuario';
            $csrf_token    = generarTokenCSRF();
            require __DIR__ . '/../views/usuarios/formulario.php';
            return;
        }

        $resultado = $this->usuarioModel->actualizar($id, $datos);

        if ($resultado === 'admin_no_degradable') {
            setMensajeError('No se puede cambiar el rol del administrador.');
            header('Location: usuarios.php');
            exit();
        }
        if ($resultado === 'admin_no_desactivable') {
            setMensajeError('No se puede desactivar al administrador.');
            header('Location: usuarios.php');
            exit();
        }
        if ($resultado === 'admin_existente') {
            setMensajeError('Ya existe un administrador en el sistema.');
            header('Location: usuarios.php');
            exit();
        }

        if ($resultado === true) {
            setMensajeExito('Usuario actualizado correctamente.');
        } else {
            setMensajeError('No se pudo actualizar el usuario.');
        }
        header('Location: usuarios.php');
        exit();
    }

    private function eliminar() {
        $this->verificarPostYCsrf();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        $resultado = $this->usuarioModel->eliminar($id);

        if ($resultado === 'admin_no_eliminable') {
            setMensajeError('No se puede eliminar al administrador del sistema.');
            header('Location: usuarios.php');
            exit();
        }
        if ($resultado === 'tiene_prestamos') {
            setMensajeError('El usuario tiene préstamos registrados. Podés desactivarlo en lugar de eliminarlo.');
            header('Location: usuarios.php');
            exit();
        }

        if ($resultado === true) {
            setMensajeExito('Usuario eliminado correctamente.');
        } else {
            setMensajeError('No se pudo eliminar el usuario.');
        }
        header('Location: usuarios.php');
        exit();
    }

    private function verificarPostYCsrf() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            setMensajeError('Método no permitido.');
            header('Location: usuarios.php');
            exit();
        }
        if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
            setMensajeError('Error de seguridad. Intente nuevamente.');
            header('Location: usuarios.php');
            exit();
        }
    }

    private function obtenerDatosFormulario() {
        $password         = $_POST['password'] ?? '';
        $passwordConfirm  = $_POST['password_confirm'] ?? '';

        return [
            'nombre'           => sanitizar($_POST['nombre'] ?? ''),
            'email'            => sanitizar($_POST['email'] ?? ''),
            'password'         => trim($password),
            'password_confirm' => trim($passwordConfirm),
            'rol'              => sanitizar($_POST['rol'] ?? 'usuario'),
            'activo'           => isset($_POST['activo']) ? 1 : 0,
        ];
    }

    private function validarDatos($datos, $idExcluir) {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        }
        if (empty($datos['email'])) {
            $errores['email'] = 'El email es obligatorio.';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El email no es válido.';
        } elseif ($this->usuarioModel->existeEmail($datos['email'], $idExcluir)) {
            $errores['email'] = 'Ya existe un usuario con ese email.';
        }

        if ($idExcluir === null) {
            if (empty($datos['password'])) {
                $errores['password'] = 'La contraseña es obligatoria.';
            } elseif (strlen($datos['password']) < 8) {
                $errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
            }
        } elseif (!empty($datos['password']) && strlen($datos['password']) < 8) {
            $errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if (!empty($datos['password']) && $datos['password'] !== $datos['password_confirm']) {
            $errores['password_confirm'] = 'Las contraseñas no coinciden.';
        }

        if (!in_array($datos['rol'], ['admin', 'bibliotecario', 'usuario'])) {
            $errores['rol'] = 'El rol seleccionado no es válido.';
        }

        return $errores;
    }
}
?>