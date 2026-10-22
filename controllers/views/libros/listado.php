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
                            <i class="bi bi-book text-primary me-2"></i>
                            <?php echo $puedeEditar ? 'Gestión de Libros' : 'Catálogo de Libros'; ?>
                        </h1>
                        <p class="text-muted mb-0">
                            <?php echo $puedeEditar
                                ? 'Administrá el catálogo de la biblioteca'
                                : 'Explorá los libros disponibles en la biblioteca'; ?>
                        </p>
                    </div>
                    <?php if ($puedeEditar): ?>
                        <a href="libros.php?accion=nuevo" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Nuevo Libro
                        </a>
                    <?php endif; ?>
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
                        <table id="tablaLibros" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>ISBN</th>
                                    <th>Categoría</th>
                                    <th>Año</th>
                                    <th>Stock</th>
                                    <?php if ($puedeEditar): ?>
                                        <th class="text-end">Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($libros as $libro): ?>
                                    <?php
                                        $stock = (int)$libro['stock_disponible'];
                                        if ($stock === 0) {
                                            $badgeClass = 'bg-secondary';
                                            $badgeText  = 'Sin stock';
                                        } elseif ($stock <= 2) {
                                            $badgeClass = 'bg-warning text-dark';
                                            $badgeText  = 'Stock bajo (' . $stock . ')';
                                        } else {
                                            $badgeClass = 'bg-success';
                                            $badgeText  = 'Disponible (' . $stock . ')';
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-muted"><?php echo $libro['id']; ?></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                        <td><?php echo $libro['isbn'] ? '<code>' . htmlspecialchars($libro['isbn']) . '</code>' : '<span class="text-muted">—</span>'; ?></td>
                                        <td>
                                            <?php if ($libro['categoria']): ?>
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                                    <?php echo htmlspecialchars($libro['categoria']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $libro['anio_publicacion'] ?: '<span class="text-muted">—</span>'; ?></td>
                                        <td>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                                            <br><small class="text-muted">Total: <?php echo $libro['stock_total']; ?></small>
                                        </td>
                                        <?php if ($puedeEditar): ?>
                                            <td class="text-end">
                                                <a href="libros.php?accion=editar&id=<?php echo $libro['id']; ?>"
                                                   class="btn btn-sm btn-outline-secondary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="libros.php?accion=eliminar" method="POST"
                                                      class="d-inline form-eliminar"
                                                      data-titulo="<?php echo htmlspecialchars($libro['titulo']); ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $libro['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        <?php endif; ?>
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
            $('#tablaLibros').DataTable({
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ libros",
                    infoEmpty: "Sin libros para mostrar",
                    infoFiltered: "(filtrado de _MAX_ libros totales)",
                    zeroRecords: "No se encontraron libros",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                order: [[1, 'asc']]
            });

            document.querySelectorAll('.form-eliminar').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const titulo = form.dataset.titulo;
                    Swal.fire({
                        title: '¿Eliminar libro?',
                        html: '¿Seguro que querés eliminar <strong>' + titulo + '</strong>?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>