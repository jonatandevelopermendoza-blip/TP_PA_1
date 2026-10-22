<?php
require_once 'funciones.php';
requerirAutenticacion();

require_once 'controllers/LibroController.php';

$controller = new LibroController();
$controller->manejarRequest();
?>