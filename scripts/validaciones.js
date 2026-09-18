document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    const usuarioInput = document.getElementById('usuario');
    const passwordInput = document.getElementById('password');
    const captchaInput = document.getElementById('captcha');
    const btnLogin = document.getElementById('btnLogin');
    const errorUsuario = document.getElementById('errorUsuario');
    const errorPassword = document.getElementById('errorPassword');
    const errorCaptcha = document.getElementById('errorCaptcha');
    const togglePassword = document.getElementById('togglePassword');
    const captchaImagen = document.getElementById('captchaImagen');
    const recargarCaptcha = document.getElementById('recargarCaptcha');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    }

    if (recargarCaptcha && captchaImagen) {
        recargarCaptcha.addEventListener('click', function() {
            captchaImagen.src = 'captcha.php?t=' + new Date().getTime();
            captchaInput.value = '';
            captchaInput.classList.remove('is-valid', 'is-invalid');
            btnLogin.disabled = true;
            captchaInput.focus();
        });
    }

    function validarFormulario() {
        const usuario = usuarioInput.value.trim();
        const password = passwordInput.value.trim();
        const captcha = captchaInput.value.trim();
        let esValido = true;

        if (usuario === '') {
            usuarioInput.classList.add('is-invalid');
            usuarioInput.classList.remove('is-valid');
            errorUsuario.textContent = 'El usuario es obligatorio';
            esValido = false;
        } else {
            usuarioInput.classList.remove('is-invalid');
            usuarioInput.classList.add('is-valid');
            errorUsuario.textContent = '';
        }

        if (password === '') {
            passwordInput.classList.add('is-invalid');
            passwordInput.classList.remove('is-valid');
            errorPassword.textContent = 'La contraseña es obligatoria';
            esValido = false;
        } else {
            passwordInput.classList.remove('is-invalid');
            passwordInput.classList.add('is-valid');
            errorPassword.textContent = '';
        }

        if (captcha === '') {
            captchaInput.classList.add('is-invalid');
            captchaInput.classList.remove('is-valid');
            errorCaptcha.textContent = 'El código captcha es obligatorio';
            esValido = false;
        } else if (captcha.length < 6) {
            captchaInput.classList.add('is-invalid');
            captchaInput.classList.remove('is-valid');
            errorCaptcha.textContent = 'El código captcha debe tener 6 caracteres';
            esValido = false;
        } else {
            captchaInput.classList.remove('is-invalid');
            captchaInput.classList.add('is-valid');
            errorCaptcha.textContent = '';
        }

        btnLogin.disabled = !esValido;
        return esValido;
    }

    [usuarioInput, passwordInput, captchaInput].forEach(input => {
        input.addEventListener('input', validarFormulario);
        input.addEventListener('blur', validarFormulario);
    });

    form.addEventListener('submit', function(e) {
        if (!validarFormulario()) {
            e.preventDefault();
            if (!usuarioInput.value.trim()) usuarioInput.focus();
            else if (!passwordInput.value.trim()) passwordInput.focus();
            else if (!captchaInput.value.trim()) captchaInput.focus();
        } else {
            btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Validando...';
            btnLogin.disabled = true;
        }
    });

    validarFormulario();
});