<?php
session_start();
// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Administración - Biblioteca Digital</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="contenedor-principal">
        <div class="login-container">
            <div class="login-box">
                <h2>Iniciar Sesión</h2>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="mensaje-error">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <form id="loginForm" action="procesoLogin.php" method="POST">
                    <div class="mb-3" >
                        <div class="campo-formulario">
                            <label for="usuario" class="form-label">Usuario:</label>
                            <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario">
                            <span class="error-mensaje" id="errorUsuario"></span>
                        </div>
                        
                        <div class="campo-formulario">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" placeholder="Ingrese su contraseña">
                            <span class="error-mensaje" id="errorPassword"></span>
                        </div>
                        
                        <button type="submit" id="btnLogin" disabled>Iniciar Sesión</button>
                    </div>    
                </form>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="scripts/validaciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>