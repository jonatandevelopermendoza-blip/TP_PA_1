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
                            <a href="libros.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Volver al listado
                            </a>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h4 class="mb-0">
                                    <i class="bi bi-<?php echo $modoEdicion ? 'pencil-square' : 'plus-circle'; ?> me-2"></i>
                                    <?php echo $modoEdicion ? 'Editar Libro' : 'Nuevo Libro'; ?>
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <?php if (!empty($errores)): ?>
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Por favor, corregí los errores marcados.
                                    </div>
                                <?php endif; ?>

                                <form action="libros.php?accion=<?php echo $modoEdicion ? 'actualizar' : 'guardar'; ?>"
                                      method="POST" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <?php if ($modoEdicion): ?>
                                        <input type="hidden" name="id" value="<?php echo $libro['id']; ?>">
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="titulo" class="form-label fw-bold">Título *</label>
                                            <input type="text" class="form-control <?php echo isset($errores['titulo']) ? 'is-invalid' : ''; ?>"
                                                   id="titulo" name="titulo"
                                                   value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
                                            <div class="invalid-feedback"><?php echo $errores['titulo'] ?? 'El título es obligatorio.'; ?></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="anio_publicacion" class="form-label fw-bold">Año</label>
                                            <input type="number" class="form-control <?php echo isset($errores['anio_publicacion']) ? 'is-invalid' : ''; ?>"
                                                   id="anio_publicacion" name="anio_publicacion"
                                                   min="1000" max="<?php echo date('Y') + 1; ?>"
                                                   value="<?php echo htmlspecialchars($libro['anio_publicacion']); ?>">
                                            <div class="invalid-feedback"><?php echo $errores['anio_publicacion'] ?? ''; ?></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="autor" class="form-label fw-bold">Autor *</label>
                                            <input type="text" class="form-control <?php echo isset($errores['autor']) ? 'is-invalid' : ''; ?>"
                                                   id="autor" name="autor"
                                                   value="<?php echo htmlspecialchars($libro['autor']); ?>" required>
                                            <div class="invalid-feedback"><?php echo $errores['autor'] ?? 'El autor es obligatorio.'; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="categoria" class="form-label fw-bold">Categoría</label>
                                            <input type="text" class="form-control"
                                                   id="categoria" name="categoria"
                                                   value="<?php echo htmlspecialchars($libro['categoria']); ?>"
                                                   placeholder="Ej: Novela, Programación, Historia...">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="isbn" class="form-label fw-bold">ISBN</label>
                                            <input type="text" class="form-control <?php echo isset($errores['isbn']) ? 'is-invalid' : ''; ?>"
                                                   id="isbn" name="isbn"
                                                   value="<?php echo htmlspecialchars($libro['isbn']); ?>"
                                                   placeholder="Opcional">
                                            <div class="invalid-feedback"><?php echo $errores['isbn'] ?? ''; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="stock_total" class="form-label fw-bold">Stock Total *</label>
                                            <input type="number" class="form-control <?php echo isset($errores['stock_total']) ? 'is-invalid' : ''; ?>"
                                                   id="stock_total" name="stock_total"
                                                   min="1"
                                                   value="<?php echo htmlspecialchars($libro['stock_total']); ?>" required>
                                            <div class="invalid-feedback"><?php echo $errores['stock_total'] ?? 'El stock debe ser al menos 1.'; ?></div>
                                            <?php if ($modoEdicion): ?>
                                                <small class="text-muted">
                                                    Ejemplares prestados actualmente: 
                                                    <strong><?php echo $libro['stock_total'] - $libro['stock_disponible']; ?></strong>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <a href="libros.php" class="btn btn-secondary">
                                            <i class="bi bi-x-lg me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i>
                                            <?php echo $modoEdicion ? 'Guardar Cambios' : 'Crear Libro'; ?>
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
</body>
</html>