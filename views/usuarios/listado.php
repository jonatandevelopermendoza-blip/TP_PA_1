<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/../../header.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <main class="flex-grow-1 py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-people text-primary me-2"></i>Gestión de Usuarios
                        </h1>
                        <p class="text-muted mb-0">Administrá los usuarios del sistema</p>
                    </div>
                    <a href="usuarios.php?accion=nuevo" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
                    </a>
                </div>

                <?php if ($mensajeExito): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($mensajeExito); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($mensajeError): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($mensajeError); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <table id="tablaUsuarios" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Alta</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $u): ?>
                                    <?php
                                        $esAdmin     = ($u['rol'] === 'admin');
                                        $tienePrest  = ((int)$u['tiene_prestamos'] > 0);
                                        $puedeBorrar = !$esAdmin && !$tienePrest;

                                        $badgeRol = match ($u['rol']) {
                                            'admin'         => 'bg-danger',
                                            'bibliotecario' => 'bg-primary',
                                            default         => 'bg-secondary',
                                        };
                                        $badgeEstado = ((int)$u['activo'] === 1)
                                            ? 'bg-success'
                                            : 'bg-secondary';
                                    ?>
                                    <tr>
                                        <td class="text-muted"><?php echo $u['id']; ?></td>
                                        <td class="fw-semibold">
                                            <?php echo htmlspecialchars($u['nombre']); ?>
                                            <?php if ((int)$u['id'] === $idLogueado): ?>
                                                <span class="badge bg-info text-dark ms-1">Vos</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td><span class="badge <?php echo $badgeRol; ?>"><?php echo ucfirst($u['rol']); ?></span></td>
                                        <td>
                                            <span class="badge <?php echo $badgeEstado; ?>">
                                                <?php echo ((int)$u['activo'] === 1) ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <?php echo date('d/m/Y', strtotime($u['created_at'])); ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="usuarios.php?accion=editar&id=<?php echo $u['id']; ?>"
                                               class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($puedeBorrar): ?>
                                                <form action="usuarios.php?accion=eliminar" method="POST"
                                                      class="d-inline form-eliminar"
                                                      data-nombre="<?php echo htmlspecialchars($u['nombre']); ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../../footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#tablaUsuarios').DataTable({
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
                    infoEmpty: "Sin usuarios para mostrar",
                    infoFiltered: "(filtrado de _MAX_ usuarios totales)",
                    zeroRecords: "No se encontraron usuarios",
                    paginate: {
                        first: "Primero", last: "Último",
                        next: "Siguiente", previous: "Anterior"
                    }
                },
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });

            document.querySelectorAll('.form-eliminar').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const nombre = form.dataset.nombre;
                    Swal.fire({
                        title: '¿Eliminar usuario?',
                        html: '¿Seguro que querés eliminar a <strong>' + nombre + '</strong>?<br><small>Esta acción no se puede deshacer.</small>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
</body>
</html>