<?php
require_once 'funciones.php';

if (estaAutenticado()) {
    header('Location: inicio.php');
    exit();
}

$csrf_token = generarTokenCSRF();
$titulo_pagina = 'Iniciar Sesión';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'header.php'; ?>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <main class="flex-grow-1 d-flex align-items-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-primary text-white text-center py-3">
                                <h4 class="mb-0">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <?php $error = getMensaje('error'); ?>
                                <?php if ($error): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <?php echo htmlspecialchars($error); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form id="loginForm" action="procesoLogin.php" method="POST" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                    <div class="mb-3">
                                        <label for="usuario" class="form-label fw-bold">
                                            <i class="bi bi-person me-1"></i>Usuario
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" id="usuario"
                                                   name="usuario" placeholder="Ingrese su usuario"
                                                   autocomplete="username" autofocus required>
                                        </div>
                                        <div class="invalid-feedback" id="errorUsuario">El usuario es obligatorio</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-bold">
                                            <i class="bi bi-lock me-1"></i>Contraseña
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                                            <input type="password" class="form-control" id="password"
                                                   name="password" placeholder="Ingrese su contraseña"
                                                   autocomplete="current-password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback" id="errorPassword">La contraseña es obligatoria</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="captcha" class="form-label fw-bold">
                                            <i class="bi bi-shield-lock me-1"></i>Verificación Captcha
                                        </label>
                                        <div class="captcha-container mb-2">
                                            <img src="captcha.php" alt="Código Captcha"
                                                 id="captchaImagen" class="captcha-imagen">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    id="recargarCaptcha" title="Generar nuevo código">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                            <input type="text" class="form-control" id="captcha"
                                                   name="captcha" placeholder="Ingrese el código de la imagen"
                                                   maxlength="6" autocomplete="off" required>
                                        </div>
                                        <div class="invalid-feedback" id="errorCaptcha">
                                            El código captcha es obligatorio
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg"
                                                id="btnLogin" disabled>
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                                        </button>
                                    </div>

                                    <div class="text-center mt-3">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            Usuario: fcytuader | Contraseña: programacionavanzada
                                        </small>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include 'footer.php'; ?>
    <script src="scripts/validaciones.js"></script>
</body>
</html>