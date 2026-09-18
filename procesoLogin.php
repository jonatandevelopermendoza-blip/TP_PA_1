<?php
require_once 'funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setMensajeError('Método no permitido');
    header('Location: index.php');
    exit();
}

if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
    setMensajeError('Error de seguridad. Intente nuevamente.');
    header('Location: index.php');
    exit();
}

$usuario  = isset($_POST['usuario'])  ? sanitizar($_POST['usuario'])   : '';
$password = isset($_POST['password']) ? trim($_POST['password'])       : '';
$captcha  = isset($_POST['captcha'])  ? sanitizar($_POST['captcha'])   : '';

if (empty($usuario) || empty($password) || empty($captcha)) {
    setMensajeError('Todos los campos son obligatorios, incluido el captcha.');
    header('Location: index.php');
    exit();
}

if (!validarCaptcha($captcha)) {
    setMensajeError('El código captcha es incorrecto o expiró. Intente nuevamente.');
    header('Location: index.php');
    exit();
}

if (validarCredenciales($usuario, $password)) {
    session_regenerate_id(true);

    $_SESSION['usuario']      = $usuario;
    $_SESSION['autenticado']  = true;
    $_SESSION['fecha_login']  = date('Y-m-d H:i:s');

    header('Location: inicio.php');
    exit();
} else {
    setMensajeError('Usuario o contraseña incorrectos. Intente nuevamente.');
    header('Location: index.php');
    exit();
}
?>