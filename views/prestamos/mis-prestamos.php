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
                <div class="mb-4">
                    <h1 class="h3 mb-1">
                        <i class="bi bi-journal-bookmark text-primary me-2"></i>Mis Préstamos
                    </h1>
                    <p class="text-muted mb-0">Historial de tus préstamos</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <table id="tablaMisPrestamos" class="table align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Libro</th>
                                    <th>Tipo</th>
                                    <th>Prestado</th>
                                    <th>Vence</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prestamos as $p): ?>
                                    <?php
                                        $activo  = ($p['estado'] === 'activo');
                                        $vencido = $activo && strtotime($p['fecha_devolucion_esperada']) < time();
                                        $esSala  = ($p['tipo'] === 'sala');

                                        if (!$activo) {
                                            $rowClass = 'table-secondary text-muted';
                                            $badgeEstado = 'bg-secondary';
                                            $textoEstado = 'Devuelto';
                                        } elseif ($vencido) {
                                            $rowClass = 'table-danger';
                                            $badgeEstado = 'bg-danger';
                                            $textoEstado = 'Atrasado';
                                        } else {
                                            $rowClass = '';
                                            $badgeEstado = 'bg-success';
                                            $textoEstado = 'Activo';
                                        }

                                        $badgeTipo = $esSala ? 'bg-warning text-dark' : 'bg-primary';
                                        $textoTipo = $esSala ? 'Sala' : 'Domiciliario';
                                    ?>
                                    <tr class="<?php echo $rowClass; ?>">
                                        <td><?php echo $p['id']; ?></td>
                                        <td class="fw-semibold">
                                            <?php echo htmlspecialchars($p['libro_titulo']); ?>
                                            <br><small class="<?php echo $activo ? 'text-muted' : ''; ?>">
                                                <?php echo htmlspecialchars($p['libro_autor']); ?>
                                            </small>
                                        </td>
                                        <td><span class="badge <?php echo $badgeTipo; ?>"><?php echo $textoTipo; ?></span></td>
                                        <td class="small"><?php echo date('d/m/Y H:i', strtotime($p['fecha_prestamo'])); ?></td>
                                        <td class="small">
                                            <?php if ($esSala): ?>
                                                <?php echo date('H:i', strtotime($p['fecha_devolucion_esperada'])); ?> hs
                                            <?php else: ?>
                                                <?php echo date('d/m/Y', strtotime($p['fecha_devolucion_esperada'])); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge <?php echo $badgeEstado; ?>"><?php echo $textoEstado; ?></span></td>
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
    <script>
        $(document).ready(function() {
            $('#tablaMisPrestamos').DataTable({
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ préstamos",
                    infoEmpty: "Sin préstamos",
                    zeroRecords: "No tenés préstamos registrados",
                    paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" }
                },
                order: [[0, 'desc']],
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        });
    </script>
</body>
</html>