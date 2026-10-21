<?php
require_once 'funciones.php';
require_once 'models/Database.php';
require_once 'models/Usuario.php';

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

$usuario  = isset($_POST['usuario'])  ? sanitizar($_POST['usuario'])  : '';
$password = isset($_POST['password']) ? trim($_POST['password'])      : '';
$captcha  = isset($_POST['captcha'])  ? sanitizar($_POST['captcha'])  : '';

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

// --- ACÁ ESTÁ EL CAMBIO PRINCIPAL ---
$usuarioModel = new Usuario();
$datosUsuario = $usuarioModel->autenticar($usuario, $password);

if ($datosUsuario) {
    // Login correcto
    session_regenerate_id(true);
    
    $_SESSION['usuario_id']     = $datosUsuario['id'];
    $_SESSION['usuario_nombre'] = $datosUsuario['nombre'];
    $_SESSION['usuario_rol']    = $datosUsuario['rol'];
    $_SESSION['autenticado']    = true;
    $_SESSION['fecha_login']    = date('Y-m-d H:i:s');
    
    // Guardamos el nombre en 'usuario' para no romper el header actual que usa $_SESSION['usuario']
    $_SESSION['usuario']        = $datosUsuario['nombre'];
    
    header('Location: inicio.php');
    exit();
} else {
    setMensajeError('Usuario o contraseña incorrectos. Intente nuevamente.');
    header('Location: index.php');
    exit();
}
?>