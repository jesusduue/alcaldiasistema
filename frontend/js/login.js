document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-login');
    const errorBox = document.getElementById('login-error');
    const btnLogin = document.getElementById('btn-login');
    const togglePassword = document.getElementById('toggle-password');
    const inputUsuario = document.getElementById('usuario');
    const inputClave = document.getElementById('clave');

    function mostrarError(mensaje) {
        errorBox.textContent = mensaje;
        errorBox.classList.remove('d-none');
    }

    function limpiarError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    if (togglePassword) {
        togglePassword.addEventListener('click', () => {
            const mostrar = inputClave.type === 'password';
            inputClave.type = mostrar ? 'text' : 'password';
            togglePassword.innerHTML = mostrar ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        limpiarError();

        const usuario = inputUsuario.value.trim();
        const clave = inputClave.value;

        if (!usuario || !clave) {
            mostrarError('Ingresa usuario y clave.');
            return;
        }

        btnLogin.disabled = true;
        btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Validando...';

        try {
            await apiRequest('auth', 'login', {
                method: 'POST',
                body: { usuario, clave },
                skipAuthRedirect: true,
            });
            window.location.href = 'index.php';
        } catch (error) {
            mostrarError(error?.message || 'No fue posible iniciar sesion.');
        } finally {
            btnLogin.disabled = false;
            btnLogin.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i>Ingresar';
        }
    });
});
