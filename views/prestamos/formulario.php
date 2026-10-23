<!DOCTYPE html>
<html lang="es">
<head>
    <?php include __DIR__ . '/../../header.php'; ?>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <main class="flex-grow-1 py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <a href="prestamos.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Volver al listado
                            </a>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h4 class="mb-0">
                                    <i class="bi bi-plus-circle me-2"></i>Nuevo Préstamo
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <?php if (!empty($errores)): ?>
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Por favor, corregí los errores.
                                    </div>
                                <?php endif; ?>

                                <div id="avisoStockBajo" class="alert alert-warning d-none">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Quedan pocos ejemplares.</strong>
                                    Este préstamo se registrará como <strong>Préstamo en Sala</strong> (devolución el mismo día, dentro de 4 horas).
                                </div>

                                <form action="prestamos.php?accion=guardar" method="POST" id="formPrestamo" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                    <div class="mb-3">
                                        <label for="usuario_id" class="form-label fw-bold">Usuario *</label>
                                        <select class="form-select <?php echo isset($errores['usuario_id']) ? 'is-invalid' : ''; ?>"
                                                id="usuario_id" name="usuario_id" required>
                                            <option value="">— Seleccioná un usuario —</option>
                                            <?php foreach ($usuarios as $u): ?>
                                                <option value="<?php echo $u['id']; ?>"
                                                        <?php echo ($datos['usuario_id'] == $u['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($u['nombre']); ?> 
                                                    (<?php echo htmlspecialchars($u['email']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"><?php echo $errores['usuario_id'] ?? 'Seleccioná un usuario.'; ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="libro_id" class="form-label fw-bold">Libro *</label>
                                        <select class="form-select <?php echo isset($errores['libro_id']) ? 'is-invalid' : ''; ?>"
                                                id="libro_id" name="libro_id" required>
                                            <option value="">— Seleccioná un libro —</option>
                                            <?php foreach ($libros as $l): ?>
                                                <option value="<?php echo $l['id']; ?>"
                                                        data-stock="<?php echo $l['stock_disponible']; ?>"
                                                        <?php echo ($datos['libro_id'] == $l['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($l['titulo']); ?> 
                                                    (Stock: <?php echo $l['stock_disponible']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"><?php echo $errores['libro_id'] ?? 'Seleccioná un libro.'; ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tipo" class="form-label fw-bold">Tipo de Préstamo *</label>
                                        <select class="form-select" id="tipo" name="tipo">
                                            <option value="domiciliario" <?php echo $datos['tipo'] === 'domiciliario' ? 'selected' : ''; ?>>
                                                Domiciliario (14 días)
                                            </option>
                                            <option value="sala" <?php echo $datos['tipo'] === 'sala' ? 'selected' : ''; ?>>
                                                Sala (4 horas)
                                            </option>
                                        </select>
                                    </div>

                                    <div class="alert alert-info" id="infoFecha">
                                        <i class="bi bi-calendar-event me-2"></i>
                                        <strong>Fecha de devolución esperada:</strong>
                                        <span id="fechaCalculada">—</span>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <a href="prestamos.php" class="btn btn-secondary">
                                            <i class="bi bi-x-lg me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i>Registrar Préstamo
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../../footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const libroSelect = document.getElementById('libro_id');
            const tipoSelect  = document.getElementById('tipo');
            const aviso       = document.getElementById('avisoStockBajo');
            const fechaTxt    = document.getElementById('fechaCalculada');
            const opcionSala  = tipoSelect.querySelector('option[value="sala"]');
            const opcionDomi  = tipoSelect.querySelector('option[value="domiciliario"]');

            function actualizarFormulario() {
                const opcion  = libroSelect.options[libroSelect.selectedIndex];
                const stock   = parseInt(opcion.dataset.stock || '0', 10);
                const hoy     = new Date();

                if (!libroSelect.value) {
                    aviso.classList.add('d-none');
                    fechaTxt.textContent = '—';
                    return;
                }

                if (stock <= 2) {
                    // Forzar sala
                    tipoSelect.value = 'sala';
                    opcionDomi.disabled = true;
                    aviso.classList.remove('d-none');

                    const devolucion = new Date(hoy.getTime() + 4 * 60 * 60 * 1000);
                    fechaTxt.textContent = devolucion.toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'}) + ' hs (hoy)';
                } else {
                    opcionDomi.disabled = false;
                    aviso.classList.add('d-none');

                    if (tipoSelect.value === 'sala') {
                        const devolucion = new Date(hoy.getTime() + 4 * 60 * 60 * 1000);
                        fechaTxt.textContent = devolucion.toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'}) + ' hs (hoy)';
                    } else {
                        const devolucion = new Date(hoy.getTime() + 14 * 24 * 60 * 60 * 1000);
                        fechaTxt.textContent = devolucion.toLocaleDateString('es-AR');
                    }
                }
            }

            libroSelect.addEventListener('change', actualizarFormulario);
            tipoSelect.addEventListener('change', actualizarFormulario);
            actualizarFormulario();
        });
    </script>
</body>
</html>