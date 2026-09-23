<?php
require_once 'funciones.php';
requerirAutenticacion();

$titulo_pagina = 'Préstamos';

$prestamos = [
    ['id' => 1, 'libro' => 'Cien años de soledad', 'usuario' => 'Carlos Ruiz', 'fecha_prestamo' => '2026-09-01', 'fecha_devolucion' => '2026-09-15', 'estado' => 'Activo'],
    ['id' => 2, 'libro' => '1984', 'usuario' => 'Lucía Fernández', 'fecha_prestamo' => '2026-08-20', 'fecha_devolucion' => '2026-09-03', 'estado' => 'Devuelto'],
    ['id' => 3, 'libro' => 'Rayuela', 'usuario' => 'Pedro Sánchez', 'fecha_prestamo' => '2026-09-05', 'fecha_devolucion' => '2026-09-19', 'estado' => 'Activo'],
    ['id' => 4, 'libro' => 'El nombre de la rosa', 'usuario' => 'María González', 'fecha_prestamo' => '2026-08-10', 'fecha_devolucion' => '2026-08-24', 'estado' => 'Vencido'],
    ['id' => 5, 'libro' => 'Ficciones', 'usuario' => 'Jonatan Mendoza', 'fecha_prestamo' => '2026-09-08', 'fecha_devolucion' => '2026-09-22', 'estado' => 'Activo'],
];
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
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-clock-history text-warning me-2"></i>Control de Préstamos
                        </h1>
                        <p class="text-muted mb-0">Gestioná préstamos y devoluciones</p>
                    </div>
                    <button class="btn btn-warning text-dark" disabled>
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Préstamo
                    </button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Total</p>
                                        <h3 class="mb-0"><?php echo count($prestamos); ?></h3>
                                    </div>
                                    <i class="bi bi-list-check fs-1 text-secondary opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Activos</p>
                                        <h3 class="mb-0 text-primary">
                                            <?php echo count(array_filter($prestamos, fn($p) => $p['estado'] === 'Activo')); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-hourglass-split fs-1 text-primary opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Devueltos</p>
                                        <h3 class="mb-0 text-success">
                                            <?php echo count(array_filter($prestamos, fn($p) => $p['estado'] === 'Devuelto')); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-check-circle fs-1 text-success opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Vencidos</p>
                                        <h3 class="mb-0 text-danger">
                                            <?php echo count(array_filter($prestamos, fn($p) => $p['estado'] === 'Vencido')); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-exclamation-triangle fs-1 text-danger opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Libro</th>
                                        <th>Usuario</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prestamos as $prestamo): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?php echo $prestamo['id']; ?></td>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($prestamo['libro']); ?></td>
                                            <td><?php echo htmlspecialchars($prestamo['usuario']); ?></td>
                                            <td class="text-muted small"><?php echo $prestamo['fecha_prestamo']; ?></td>
                                            <td class="text-muted small"><?php echo $prestamo['fecha_devolucion']; ?></td>
                                            <td>
                                                <?php
                                                $badgeEstado = match ($prestamo['estado']) {
                                                    'Activo' => 'bg-primary-subtle text-primary-emphasis',
                                                    'Devuelto' => 'bg-success-subtle text-success-emphasis',
                                                    'Vencido' => 'bg-danger-subtle text-danger-emphasis',
                                                    default => 'bg-secondary-subtle text-secondary-emphasis',
                                                };
                                                $iconoEstado = match ($prestamo['estado']) {
                                                    'Activo' => 'bi-hourglass-split',
                                                    'Devuelto' => 'bi-check-circle',
                                                    'Vencido' => 'bi-exclamation-triangle',
                                                    default => 'bi-circle',
                                                };
                                                ?>
                                                <span class="badge <?php echo $badgeEstado; ?>">
                                                    <i class="bi <?php echo $iconoEstado; ?> me-1"></i>
                                                    <?php echo htmlspecialchars($prestamo['estado']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="Ver detalle">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" disabled title="Registrar devolución">
                                                    <i class="bi bi-box-arrow-in-left"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <p class="text-muted small text-center mt-4">
                    <i class="bi bi-info-circle me-1"></i>
                    Vista de ejemplo — los datos se conectarán a la base de datos en la próxima entrega.
                </p>
            </div>
        </main>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>