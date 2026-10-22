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
                            <a href="usuarios.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Volver al listado
                            </a>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h4 class="mb-0">
                                    <i class="bi bi-<?php echo $modoEdicion ? 'person-gear' : 'person-plus'; ?> me-2"></i>
                                    <?php echo $modoEdicion ? 'Editar Usuario' : 'Nuevo Usuario'; ?>
                                </h4>
                            </div>
                            <div class="card-body p-4">
                                <?php if (!empty($errores)): ?>
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Por favor, corregí los errores marcados.
                                    </div>
                                <?php endif; ?>

                                <?php if ($esAdminTarget && $modoEdicion): ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Estás editando al <strong>administrador del sistema</strong>.
                                        No se puede cambiar su rol ni desactivarlo.
                                    </div>
                                <?php endif; ?>

                                <form action="usuarios.php?accion=<?php echo $modoEdicion ? 'actualizar' : 'guardar'; ?>"
                                      method="POST" novalidate id="formUsuario">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <?php if ($modoEdicion): ?>
                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nombre" class="form-label fw-bold">Nombre *</label>
                                            <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>"
                                                   id="nombre" name="nombre"
                                                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                            <div class="invalid-feedback"><?php echo $errores['nombre'] ?? 'El nombre es obligatorio.'; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label fw-bold">Email *</label>
                                            <input type="email" class="form-control <?php echo isset($errores['email']) ? 'is-invalid' : ''; ?>"
                                                   id="email" name="email"
                                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                            <div class="invalid-feedback"><?php echo $errores['email'] ?? 'El email es obligatorio.'; ?></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label fw-bold">
                                                Contraseña <?php echo $modoEdicion ? '' : '*'; ?>
                                            </label>
                                            <input type="password" class="form-control <?php echo isset($errores['password']) ? 'is-invalid' : ''; ?>"
                                                   id="password" name="password"
                                                   placeholder="<?php echo $modoEdicion ? 'Dejar en blanco para no cambiar' : 'Mínimo 8 caracteres'; ?>"
                                                   <?php echo $modoEdicion ? '' : 'required'; ?>>
                                            <div class="invalid-feedback"><?php echo $errores['password'] ?? 'Mínimo 8 caracteres.'; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirm" class="form-label fw-bold">
                                                Confirmar Contraseña <?php echo $modoEdicion ? '' : '*'; ?>
                                            </label>
                                            <input type="password" class="form-control <?php echo isset($errores['password_confirm']) ? 'is-invalid' : ''; ?>"
                                                   id="password_confirm" name="password_confirm"
                                                   placeholder="Repetí la contraseña"
                                                   <?php echo $modoEdicion ? '' : 'required'; ?>>
                                            <div class="invalid-feedback"><?php echo $errores['password_confirm'] ?? 'Las contraseñas no coinciden.'; ?></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rol" class="form-label fw-bold">Rol *</label>
                                            <select class="form-select <?php echo isset($errores['rol']) ? 'is-invalid' : ''; ?>"
                                                    id="rol" name="rol"
                                                    <?php echo $esAdminTarget ? 'disabled' : ''; ?>>
                                                <?php if ($esAdminTarget): ?>
                                                    <option value="admin" selected>Administrador</option>
                                                <?php else: ?>
                                                    <option value="usuario"         <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                                    <option value="bibliotecario"   <?php echo $usuario['rol'] === 'bibliotecario' ? 'selected' : ''; ?>>Bibliotecario</option>
                                                <?php endif; ?>
                                            </select>
                                            <?php if ($esAdminTarget): ?>
                                                <input type="hidden" name="rol" value="admin">
                                            <?php endif; ?>
                                            <div class="invalid-feedback"><?php echo $errores['rol'] ?? ''; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3 d-flex align-items-end">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                       id="activo" name="activo" value="1"
                                                       <?php echo $usuario['activo'] ? 'checked' : ''; ?>
                                                       <?php echo $esAdminTarget ? 'disabled' : ''; ?>>
                                                <label class="form-check-label fw-bold" for="activo">
                                                    Usuario activo
                                                </label>
                                                <?php if ($esAdminTarget): ?>
                                                    <input type="hidden" name="activo" value="1">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <a href="usuarios.php" class="btn btn-secondary">
                                            <i class="bi bi-x-lg me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i>
                                            <?php echo $modoEdicion ? 'Guardar Cambios' : 'Crear Usuario'; ?>
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
            const form = document.getElementById('formUsuario');
            const password = document.getElementById('password');
            const confirm  = document.getElementById('password_confirm');
            const nombre   = document.getElementById('nombre');
            const email    = document.getElementById('email');

            function validarPassword() {
                const val = password.value;
                if (val === '') return true;
                if (val.length < 8) {
                    password.classList.add('is-invalid');
                    return false;
                }
                password.classList.remove('is-invalid');
                return true;
            }

            function validarConfirm() {
                if (confirm.value === '' && password.value === '') return true;
                if (confirm.value !== password.value) {
                    confirm.classList.add('is-invalid');
                    return false;
                }
                confirm.classList.remove('is-invalid');
                return true;
            }

            password.addEventListener('input', validarPassword);
            confirm.addEventListener('input', validarConfirm);
            password.addEventListener('input', validarConfirm);

            form.addEventListener('submit', function(e) {
                let ok = true;
                if (nombre.value.trim() === '') {
                    nombre.classList.add('is-invalid'); ok = false;
                } else nombre.classList.remove('is-invalid');

                if (email.value.trim() === '') {
                    email.classList.add('is-invalid'); ok = false;
                } else email.classList.remove('is-invalid');

                if (!validarPassword()) ok = false;
                if (!validarConfirm())  ok = false;

                if (!ok) e.preventDefault();
            });
        });
    </script>
</body>
</html>