document.addEventListener('DOMContentLoaded', function() {
    const usuarioInput = document.getElementById('usuario');
    const passwordInput = document.getElementById('password');
    const btnLogin = document.getElementById('btnLogin');
    const errorUsuario = document.getElementById('errorUsuario');
    const errorPassword = document.getElementById('errorPassword');
    const loginForm = document.getElementById('loginForm');

    // Función para validar el formulario
    function validarFormulario() {
        const usuario = usuarioInput.value.trim();
        const password = passwordInput.value.trim();
        
        let esValido = true;
        
        // Validar usuario
        if (usuario === '') {
            errorUsuario.textContent = 'El usuario es obligatorio';
            usuarioInput.style.borderColor = '#e74c3c';
            esValido = false;
        } else {
            errorUsuario.textContent = '';
            usuarioInput.style.borderColor = '#ddd';
        }
        
        // Validar contraseña
        if (password === '') {
            errorPassword.textContent = 'La contraseña es obligatoria';
            passwordInput.style.borderColor = '#e74c3c';
            esValido = false;
        } else {
            errorPassword.textContent = '';
            passwordInput.style.borderColor = '#ddd';
        }
        
        // Habilitar/deshabilitar botón
        btnLogin.disabled = !esValido;
        
        return esValido;
    }

    // Eventos para validación en tiempo real
    usuarioInput.addEventListener('input', validarFormulario);
    passwordInput.addEventListener('input', validarFormulario);
    usuarioInput.addEventListener('blur', validarFormulario);
    passwordInput.addEventListener('blur', validarFormulario);

    // Validación antes del envío
    loginForm.addEventListener('submit', function(e) {
        if (!validarFormulario()) {
            e.preventDefault();
        }
    });

    // Validación inicial
    validarFormulario();
});