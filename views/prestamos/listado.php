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
                            <i class="bi bi-clock-history text-primary me-2"></i>Control de Préstamos
                        </h1>
                        <p class="text-muted mb-0">Gestioná préstamos y devoluciones</p>
                    </div>
                    <a href="prestamos.php?accion=nuevo" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Préstamo
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
                        <table id="tablaPrestamos" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Libro</th>
                                    <th>Usuario</th>
                                    <th>Tipo</th>
                                    <th>Prestado</th>
                                    <th>Devolver</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamos as $p): ?>
                                    <?php
                                        $activo   = ($p['estado'] === 'activo');
                                        $vencido  = $activo && strtotime($p['fecha_devolucion_esperada']) < time();
                                        $esSala   = ($p['tipo'] === 'sala');

                                        $badgeTipo = $esSala ? 'bg-warning text-dark' : 'bg-primary';
                                        $textoTipo = $esSala ? 'Sala' : 'Domiciliario';

                                        if (!$activo) {
                                            $badgeEstado = 'bg-secondary';
                                            $textoEstado = 'Devuelto';
                                        } elseif ($vencido) {
                                            $badgeEstado = 'bg-danger';
                                            $textoEstado = 'Vencido';
                                        } else {
                                            $badgeEstado = 'bg-success';
                                            $textoEstado = 'Activo';
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-muted"><?php echo $p['id']; ?></td>
                                        <td class="fw-semibold">
                                            <?php echo htmlspecialchars($p['libro_titulo']); ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($p['libro_autor']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($p['usuario_nombre']); ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($p['usuario_email']); ?></small>
                                        </td>
                                        <td><span class="badge <?php echo $badgeTipo; ?>"><?php echo $textoTipo; ?></span></td>
                                        <td class="small"><?php echo date('d/m/Y H:i', strtotime($p['fecha_prestamo'])); ?></td>
                                        <td class="small">
                                            <?php if ($esSala): ?>
                                                <?php echo date('H:i', strtotime($p['fecha_devolucion_esperada'])); ?> hs
                                            <?php else: ?>
                                                <?php echo date('d/m/Y', strtotime($p['fecha_devolucion_esperada'])); ?>
                                            <?php endif; ?>
                                            <?php if ($p['fecha_devolucion_real']): ?>
                                                <br><small class="text-success">Devuelto: <?php echo date('d/m/Y H:i', strtotime($p['fecha_devolucion_real'])); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge <?php echo $badgeEstado; ?>"><?php echo $textoEstado; ?></span></td>
                                        <td class="text-end">
                                            <?php if ($activo): ?>
                                                <form action="prestamos.php?accion=devolver" method="POST"
                                                      class="d-inline form-devolver"
                                                      data-libro="<?php echo htmlspecialchars($p['libro_titulo']); ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Registrar devolución">
                                                        <i class="bi bi-box-arrow-in-left"></i> Devolver
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
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
            $('#tablaPrestamos').DataTable({
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ préstamos",
                    infoEmpty: "Sin préstamos",
                    infoFiltered: "(filtrado de _MAX_ totales)",
                    zeroRecords: "No se encontraron préstamos",
                    paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" }
                },
                order: [[0, 'desc']],
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            document.querySelectorAll('.form-devolver').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const libro = form.dataset.libro;
                    Swal.fire({
                        title: '¿Registrar devolución?',
                        html: 'Se marcará como devuelto el ejemplar de <strong>' + libro + '</strong>.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, devolver',
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