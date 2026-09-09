<header>
    <div class="header-container">
        <div class="logo">
            <img src="imagenes/logo.png" alt="Logo Biblioteca Digital" height="50">
            <span>Biblioteca Digital</span>
        </div>
        <nav>
            <ul>
                <?php if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true): ?>
                    <li><a href="dashboard.php">Inicio</a></li>
                    <li><a href="#">Libros</a></li>
                    <li><a href="#">Usuarios</a></li>
                    <li><a href="#">Préstamos</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="index.php">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>