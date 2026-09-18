<?php
require_once __DIR__ . '/config.php';

function estaAutenticado() {
    return isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true;
}

function requerirAutenticacion() {
    if (!estaAutenticado()) {
        setMensajeError('Debe iniciar sesión para acceder a esta página');
        header('Location: index.php');
        exit();
    }
}

function sanitizar($dato) {
    return htmlspecialchars(trim(stripslashes($dato)), ENT_QUOTES, 'UTF-8');
}

function validarCredenciales($usuario, $password) {
    return $usuario === USUARIO_VALIDO && $password === PASSWORD_VALIDO;
}

function validarCaptcha($captchaIngresado) {
    if (!isset($_SESSION['captcha_codigo']) || !isset($_SESSION['captcha_tiempo'])) {
        return false;
    }

    if ((time() - $_SESSION['captcha_tiempo']) > CAPTCHA_TIEMPO_EXPIRACION) {
        unset($_SESSION['captcha_codigo'], $_SESSION['captcha_tiempo']);
        return false;
    }

    $valido = strtoupper(trim($captchaIngresado)) === strtoupper($_SESSION['captcha_codigo']);

    unset($_SESSION['captcha_codigo'], $_SESSION['captcha_tiempo']);

    return $valido;
}

function setMensajeExito($m) { $_SESSION['exito'] = $m; }
function setMensajeError($m) { $_SESSION['error'] = $m; }

function getMensaje($tipo) {
    if (isset($_SESSION[$tipo])) {
        $m = $_SESSION[$tipo];
        unset($_SESSION[$tipo]);
        return $m;
    }
    return null;
}

function generarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function cerrarSesion() {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }

    session_destroy();
}
?>