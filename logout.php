<?php
require_once 'funciones.php';

cerrarSesion();

session_start();
setMensajeExito('Has cerrado sesión correctamente');

header('Location: index.php');
exit();
?>