<?php
require_once 'funciones.php';
requerirAutenticacion();

$titulo_pagina = 'Usuarios';

$usuarios = [
    ['id' => 1, 'nombre' => 'Jonatan Mendoza', 'email' => 'jonatan@fcyt.edu.ar', 'rol' => 'Administrador', 'activo' => true, 'alta' => '2024-03-15'],
    ['id' => 2, 'nombre' => 'María González', 'email' => 'maria@fcyt.edu.ar', 'rol' => 'Bibliotecario', 'activo' => true, 'alta' => '2024-04-02'],
    ['id' => 3, 'nombre' => 'Carlos Ruiz', 'email' => 'carlos@fcyt.edu.ar', 'rol' => 'Usuario', 'activo' => true, 'alta' => '2024-05-20'],
    ['id' => 4, 'nombre' => 'Lucía Fernández', 'email' => 'lucia@fcyt.edu.ar', 'rol' => 'Usuario', 'activo' => false, 'alta' => '2024-06-10'],
    ['id' => 5, 'nombre' => 'Pedro Sánchez', 'email' => 'pedro@fcyt.edu.ar', 'rol' => 'Bibliotecario', 'activo' => true, 'alta' => '2024-07-01'],
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
                            <i class="bi bi-people text-success me-2"></i>Gestión de Usuarios
                        </h1>
                        <p class="text-muted mb-0">Administrá los usuarios del sistema</p>
                    </div>
                    <button class="btn btn-success" disabled>
                        <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
                    </button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Total</p>
                                        <h3 class="mb-0"><?php echo count($usuarios); ?></h3>
                                    </div>
                                    <i class="bi bi-people fs-1 text-success opacity-25"></i>
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
                                        <h3 class="mb-0 text-success">
                                            <?php echo count(array_filter($usuarios, fn($u) => $u['activo'])); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-person-check fs-1 text-success opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Administradores</p>
                                        <h3 class="mb-0 text-primary">
                                            <?php echo count(array_filter($usuarios, fn($u) => $u['rol'] === 'Administrador')); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-shield-lock fs-1 text-primary opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Inactivos</p>
                                        <h3 class="mb-0 text-danger">
                                            <?php echo count(array_filter($usuarios, fn($u) => !$u['activo'])); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-person-x fs-1 text-danger opacity-25"></i>
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
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Alta</th>
                                        <th>Estado</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?php echo $usuario['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                                                    </div>
                                                    <span class="fw-semibold"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <td>
                                                <?php
                                                $badgeRol = match ($usuario['rol']) {
                                                    'Administrador' => 'bg-danger-subtle text-danger-emphasis',
                                                    'Bibliotecario' => 'bg-primary-subtle text-primary-emphasis',
                                                    default => 'bg-secondary-subtle text-secondary-emphasis',
                                                };
                                                ?>
                                                <span class="badge <?php echo $badgeRol; ?>">
                                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                                </span>
                                            </td>
                                            <td class="text-muted small"><?php echo $usuario['alta']; ?></td>
                                            <td>
                                                <?php if ($usuario['activo']): ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis">
                                                        <i class="bi bi-check-circle me-1"></i>Activo
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis">
                                                        <i class="bi bi-x-circle me-1"></i>Inactivo
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" disabled title="Eliminar">
                                                    <i class="bi bi-trash"></i>
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