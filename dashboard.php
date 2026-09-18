<?php
require_once 'funciones.php';
requerirAutenticacion();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Biblioteca Digital</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="contenedor-principal">
        <div class="dashboard-container">
            <div class="mensaje-bienvenida">
                <h1>¡<?php echo $_SESSION['mensaje']; ?>!</h1>
                <p>Bienvenido al sistema de administración de la Biblioteca Digital</p>
            </div>
            
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Libros</h3>
                    <p>Gestión de libros</p>
                    <a href="#" class="btn-card">Administrar</a>
                </div>
                <div class="card">
                    <h3>Usuarios</h3>
                    <p>Gestión de usuarios</p>
                    <a href="#" class="btn-card">Administrar</a>
                </div>
                <div class="card">
                    <h3>Préstamos</h3>
                    <p>Control de préstamos</p>
                    <a href="#" class="btn-card">Administrar</a>
                </div>
            </div>
            
            <div class="cerrar-sesion">
                <a href="logout.php" class="btn-cerrar">Cerrar Sesión</a>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>