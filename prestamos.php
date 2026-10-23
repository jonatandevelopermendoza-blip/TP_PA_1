<?php
require_once 'funciones.php';
requerirAutenticacion();

require_once 'controllers/PrestamoController.php';

$controller = new PrestamoController();
$controller->manejarRequest();
?>