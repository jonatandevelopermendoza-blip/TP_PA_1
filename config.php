<?php
define('APP_NAME', 'Biblioteca Digital');
define('APP_VERSION', '2.0.0');

define('USUARIO_VALIDO', 'fcytuader');
define('PASSWORD_VALIDO', 'programacionavanzada');

define('CAPTCHA_TIEMPO_EXPIRACION', 300);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Argentina/Buenos_Aires');
?>