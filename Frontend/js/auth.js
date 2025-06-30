document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const authBtn = document.getElementById('authBtn');
    const authModal = document.getElementById('authModal');
    const closeModal = document.querySelector('.close');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showRegister = document.getElementById('showRegister');
    const showLogin = document.getElementById('showLogin');
    const loginFormElement = document.getElementById('loginFormElement');
    const registerFormElement = document.getElementById('registerFormElement');

    // Verificar estado de sesión al cargar
    checkAuthStatus();

    // Event Listeners
    authBtn.addEventListener('click', handleAuthClick);
    closeModal.addEventListener('click', closeAuthModal);
    showRegister.addEventListener('click', showRegisterForm);
    showLogin.addEventListener('click', showLoginForm);
    loginFormElement.addEventListener('submit', handleLogin);
    registerFormElement.addEventListener('submit', handleRegister);

    // Cerrar modal al hacer click fuera
    window.addEventListener('click', function(event) {
        if (event.target === authModal) {
            closeAuthModal();
        }
    });

    function checkAuthStatus() {
        const userData = localStorage.getItem('userData');
        if (userData) {
            const user = JSON.parse(userData);
            updateAuthButton(true, user.nombre);
        } else {
            updateAuthButton(false);
        }
    }

    function updateAuthButton(isLoggedIn, userName = '') {
        if (isLoggedIn) {
            authBtn.textContent = `Cerrar Sesión (${userName})`;
            authBtn.onclick = logout;
        } else {
            authBtn.textContent = 'Iniciar Sesión';
            authBtn.onclick = handleAuthClick;
        }
    }

    function handleAuthClick(e) {
        e.preventDefault();
        authModal.style.display = 'block';
        showLoginForm();
    }

    function closeAuthModal() {
        authModal.style.display = 'none';
        clearForms();
    }

    function showRegisterForm(e) {
        if (e) e.preventDefault();
        loginForm.classList.remove('active');
        registerForm.classList.add('active');
    }

    function showLoginForm(e) {
        if (e) e.preventDefault();
        registerForm.classList.remove('active');
        loginForm.classList.add('active');
    }

    function clearForms() {
        loginFormElement.reset();
        registerFormElement.reset();
        hideMessages();
    }

    async function handleLogin(e) {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        if (!email || !password) {
            showError('Por favor, completa todos los campos');
            return;
        }

        try {
            setLoading(true);
            
            const response = await fetch('/estudio/Backend/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (data.success) {
                // Guardar datos del usuario
                localStorage.setItem('userData', JSON.stringify(data.user));
                
                showSuccess('¡Inicio de sesión exitoso!');
                
                setTimeout(() => {
                    closeAuthModal();
                    
                    // Redireccionar según el rol
                    if (data.user.rol === 'administrador') {
                        window.location.href = '/estudio/Frontend/web/admin/dashboard.html';
                    } else {
                        // Permanecer en la página actual (cliente)
                        updateAuthButton(true, data.user.nombre);
                    }
                }, 1500);
            } else {
                showError(data.message || 'Error al iniciar sesión');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Error de conexión. Inténtalo de nuevo.');
        } finally {
            setLoading(false);
        }
    }

    async function handleRegister(e) {
        e.preventDefault();
        
        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!name || !email || !password || !confirmPassword) {
            showError('Por favor, completa todos los campos');
            return;
        }

        if (password !== confirmPassword) {
            showError('Las contraseñas no coinciden');
            return;
        }

        if (password.length < 6) {
            showError('La contraseña debe tener al menos 6 caracteres');
            return;
        }

        try {
            setLoading(true);
            
            const response = await fetch('/estudio/Backend/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name, email, password })
            });

            const data = await response.json();

            if (data.success) {
                showSuccess('¡Registro exitoso! Ahora puedes iniciar sesión.');
                setTimeout(() => {
                    showLoginForm();
                }, 2000);
            } else {
                showError(data.message || 'Error al registrarse');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Error de conexión. Inténtalo de nuevo.');
        } finally {
            setLoading(false);
        }
    }

    function logout() {
        localStorage.removeItem('userData');
        updateAuthButton(false);
        
        // Si está en página de admin, redirigir al inicio
        if (window.location.pathname.includes('admin')) {
            window.location.href = '/estudio/index.html';
        }
    }

    function setLoading(loading) {
        const forms = document.querySelectorAll('.auth-form');
        forms.forEach(form => {
            if (loading) {
                form.classList.add('loading');
            } else {
                form.classList.remove('loading');
            }
        });
    }

    function showError(message) {
        hideMessages();
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        
        const activeForm = document.querySelector('.auth-form.active');
        activeForm.insertBefore(errorDiv, activeForm.querySelector('form'));
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    function showSuccess(message) {
        hideMessages();
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.textContent = message;
        successDiv.style.display = 'block';
        
        const activeForm = document.querySelector('.auth-form.active');
        activeForm.insertBefore(successDiv, activeForm.querySelector('form'));
    }

    function hideMessages() {
        const messages = document.querySelectorAll('.error-message, .success-message');
        messages.forEach(msg => msg.remove());
    }
});