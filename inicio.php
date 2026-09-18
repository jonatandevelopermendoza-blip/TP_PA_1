<?php
require_once 'funciones.php';

requerirAutenticacion();

$nombreUsuario = $_SESSION['usuario'] ?? 'Invitado';
$titulo_pagina = 'Inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'header.php'; ?>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <main class="flex-grow-1 py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow border-0">
                            <div class="card-body text-center p-5">
                                <div class="display-1 text-primary mb-4">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <h1 class="display-5 fw-bold text-primary mb-3">
                                    ¡Bienvenido a <?php echo APP_NAME; ?>!
                                </h1>
                                <p class="lead text-muted mb-4">
                                    Nos alegra tenerte de vuelta,
                                    <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>.
                                </p>
                                <hr class="my-4">
                                <p class="mb-4">
                                    Has iniciado sesión correctamente en nuestro sistema
                                    de administración. Desde aquí podrás gestionar libros,
                                    usuarios y préstamos de manera sencilla y eficiente.
                                </p>
                                <div class="row mt-4">
                                    <div class="col-md-4 mb-3">
                                        <a href="dashboard.php" class="btn btn-outline-primary w-100 py-3">
                                            <i class="bi bi-speedometer2 fs-4 d-block mb-1"></i>
                                            Dashboard
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="#" class="btn btn-outline-success w-100 py-3">
                                            <i class="bi bi-people fs-4 d-block mb-1"></i>
                                            Usuarios
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="#" class="btn btn-outline-warning w-100 py-3">
                                            <i class="bi bi-clock-history fs-4 d-block mb-1"></i>
                                            Préstamos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>