<?php
require_once 'funciones.php';
requerirAutenticacion();

$titulo_pagina = 'Libros';

$libros = [
    ['id' => 1, 'titulo' => 'Cien años de soledad', 'autor' => 'Gabriel García Márquez', 'isbn' => '978-0307474728', 'categoria' => 'Novela', 'disponible' => true],
    ['id' => 2, 'titulo' => '1984', 'autor' => 'George Orwell', 'isbn' => '978-0451524935', 'categoria' => 'Ciencia Ficción', 'disponible' => true],
    ['id' => 3, 'titulo' => 'El nombre de la rosa', 'autor' => 'Umberto Eco', 'isbn' => '978-0156001311', 'categoria' => 'Misterio', 'disponible' => false],
    ['id' => 4, 'titulo' => 'Rayuela', 'autor' => 'Julio Cortázar', 'isbn' => '978-8437604572', 'categoria' => 'Novela', 'disponible' => true],
    ['id' => 5, 'titulo' => 'Ficciones', 'autor' => 'Jorge Luis Borges', 'isbn' => '978-0802130303', 'categoria' => 'Cuento', 'disponible' => true],
    ['id' => 6, 'titulo' => 'La casa de los espíritus', 'autor' => 'Isabel Allende', 'isbn' => '978-1501117015', 'categoria' => 'Novela', 'disponible' => false],
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
                            <i class="bi bi-book text-primary me-2"></i>Gestión de Libros
                        </h1>
                        <p class="text-muted mb-0">Administrá el catálogo de la biblioteca</p>
                    </div>
                    <button class="btn btn-primary" disabled>
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Libro
                    </button>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Total</p>
                                        <h3 class="mb-0"><?php echo count($libros); ?></h3>
                                    </div>
                                    <i class="bi bi-collection fs-1 text-primary opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Disponibles</p>
                                        <h3 class="mb-0 text-success">
                                            <?php echo count(array_filter($libros, fn($l) => $l['disponible'])); ?>
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
                                        <p class="text-muted mb-1 small">Prestados</p>
                                        <h3 class="mb-0 text-warning">
                                            <?php echo count(array_filter($libros, fn($l) => !$l['disponible'])); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-hourglass-split fs-1 text-warning opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1 small">Categorías</p>
                                        <h3 class="mb-0 text-info">
                                            <?php echo count(array_unique(array_column($libros, 'categoria'))); ?>
                                        </h3>
                                    </div>
                                    <i class="bi bi-tags fs-1 text-info opacity-25"></i>
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
                                        <th>Título</th>
                                        <th>Autor</th>
                                        <th>ISBN</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($libros as $libro): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?php echo $libro['id']; ?></td>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                            <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                            <td><code><?php echo htmlspecialchars($libro['isbn']); ?></code></td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                                    <?php echo htmlspecialchars($libro['categoria']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($libro['disponible']): ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis">
                                                        <i class="bi bi-check-circle me-1"></i>Disponible
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis">
                                                        <i class="bi bi-hourglass-split me-1"></i>Prestado
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