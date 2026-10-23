<?php
require_once 'funciones.php';
require_once 'models/Database.php';
require_once 'models/Libro.php';
require_once 'models/Usuario.php';
require_once 'models/Prestamo.php';

requerirAutenticacion();

$nombreUsuario = $_SESSION['usuario'] ?? 'Invitado';
$rol           = $_SESSION['usuario_rol'] ?? 'usuario';
$idLogueado    = (int)($_SESSION['usuario_id'] ?? 0);
$titulo_pagina = 'Inicio';

$db = Database::getConnection();

// Estadísticas según rol
$stats = [];
$acciones = [];

if ($rol === 'admin') {
    $stmt = $db->query("SELECT COUNT(*) FROM libros");
    $stats['libros'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM usuarios");
    $stats['usuarios'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM usuarios WHERE activo = 1");
    $stats['usuarios_activos'] = (int)$stmt->fetchColumn();

    $acciones = [
        ['label' => 'Gestionar Usuarios', 'url' => 'usuarios.php', 'icon' => 'bi-people', 'color' => 'success'],
        ['label' => 'Ver Catálogo',       'url' => 'libros.php',   'icon' => 'bi-book',   'color' => 'primary'],
    ];
} elseif ($rol === 'bibliotecario') {
    $stmt = $db->query("SELECT COUNT(*) FROM libros");
    $stats['libros'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM prestamos WHERE estado = 'activo'");
    $stats['prestamos_activos'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM prestamos WHERE estado = 'activo' AND fecha_devolucion_esperada < NOW()");
    $stats['prestamos_vencidos'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM libros WHERE stock_disponible <= 2 AND stock_disponible > 0");
    $stats['stock_bajo'] = (int)$stmt->fetchColumn();

    $acciones = [
        ['label' => 'Gestionar Libros', 'url' => 'libros.php',    'icon' => 'bi-book',           'color' => 'primary'],
        ['label' => 'Ver Préstamos',    'url' => 'prestamos.php', 'icon' => 'bi-clock-history',  'color' => 'warning'],
    ];
} else {
    $prestamoModel = new Prestamo();

    $stmt = $db->prepare("SELECT COUNT(*) FROM prestamos WHERE usuario_id = :uid AND estado = 'activo'");
    $stmt->execute([':uid' => $idLogueado]);
    $stats['activos'] = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM prestamos WHERE usuario_id = :uid AND estado = 'activo' AND fecha_devolucion_esperada < NOW()");
    $stmt->execute([':uid' => $idLogueado]);
    $stats['atrasados'] = (int)$stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM prestamos WHERE usuario_id = :uid AND estado = 'devuelto'");
    $stmt->execute([':uid' => $idLogueado]);
    $stats['devueltos'] = (int)$stmt->fetchColumn();

    $acciones = [
        ['label' => 'Ver Catálogo',  'url' => 'libros.php',         'icon' => 'bi-book',              'color' => 'primary'],
        ['label' => 'Mis Préstamos', 'url' => 'mis-prestamos.php',  'icon' => 'bi-journal-bookmark',  'color' => 'success'],
    ];
}
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
                    <div class="col-lg-9">
                        <div class="card shadow border-0 mb-4">
                            <div class="card-body text-center p-5">
                                <div class="display-1 text-primary mb-4">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <h1 class="display-5 fw-bold text-primary mb-3">
                                    ¡Bienvenido a <?php echo APP_NAME; ?>!
                                </h1>
                                <p class="lead text-muted mb-3">
                                    Nos alegra tenerte de vuelta,
                                    <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>.
                                </p>
                                <span class="badge bg-info text-dark fs-6">
                                    <?php echo ucfirst($rol); ?>
                                </span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <?php if ($rol === 'admin'): ?>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-book fs-1 text-primary"></i>
                                            <h3 class="mt-2 mb-0"><?php echo $stats['libros']; ?></h3>
                                            <small class="text-muted">Libros en catálogo</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-people fs-1 text-success"></i>
                                            <h3 class="mt-2 mb-0"><?php echo $stats['usuarios']; ?></h3>
                                            <small class="text-muted">Usuarios totales</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-person-check fs-1 text-info"></i>
                                            <h3 class="mt-2 mb-0"><?php echo $stats['usuarios_activos']; ?></h3>
                                            <small class="text-muted">Usuarios activos</small>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($rol === 'bibliotecario'): ?>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-book fs-2 text-primary"></i>
                                            <h4 class="mt-2 mb-0"><?php echo $stats['libros']; ?></h4>
                                            <small class="text-muted">Libros</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-clock-history fs-2 text-success"></i>
                                            <h4 class="mt-2 mb-0"><?php echo $stats['prestamos_activos']; ?></h4>
                                            <small class="text-muted">Préstamos activos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
                                            <h4 class="mt-2 mb-0"><?php echo $stats['prestamos_vencidos']; ?></h4>
                                            <small class="text-muted">Vencidos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-box-seam fs-2 text-warning"></i>
                                            <h4 class="mt-2 mb-0"><?php echo $stats['stock_bajo']; ?></h4>
                                            <small class="text-muted">Stock bajo (≤2)</small>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-journal-check fs-1 text-success"></i>
                                            <h3 class="mt-2 mb-0"><?php echo $stats['activos']; ?></h3>
                                            <small class="text-muted">Préstamos activos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                                            <h3 class="mt-2 mb-0"><?php echo $stats['atrasados']; ?></h3>
                                            <small class="text-muted">Atrasados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-journal-check fs-1 text-secondary"></i>
                                            <h3 class="mt-2 mb-0"><?php echo $stats['devueltos']; ?></h3>
                                            <small class="text-muted">Devueltos</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row g-3">
                            <?php foreach ($acciones as $accion): ?>
                                <div class="col-md-6">
                                    <a href="<?php echo $accion['url']; ?>"
                                       class="btn btn-outline-<?php echo $accion['color']; ?> w-100 py-3">
                                        <i class="bi <?php echo $accion['icon']; ?> fs-4 d-block mb-1"></i>
                                        <?php echo $accion['label']; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>