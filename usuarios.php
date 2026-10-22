<?php
require_once 'funciones.php';
requerirAutenticacion();

require_once 'controllers/UsuarioController.php';

$controller = new UsuarioController();
$controller->manejarRequest();