<?php
session_start();

// Verificar que se hayan enviado datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Credenciales correctas
    $usuario_valido = 'fcytuader';
    $password_valida = 'programacionavanzada';
    
    // Validar credenciales
    if ($usuario === $usuario_valido && $password === $password_valida) {
        // Autenticación exitosa
        $_SESSION['usuario'] = $usuario;
        $_SESSION['autenticado'] = true;
        $_SESSION['mensaje'] = 'Ingreso correctamente';
        
        // Redirigir al dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        // Autenticación fallida
        $_SESSION['error'] = 'Usuario o contraseña incorrectos. Intente nuevamente.';
        header('Location: index.php');
        exit();
    }
} else {
    // Si no es POST, redirigir al login
    header('Location: index.php');
    exit();
}
?>