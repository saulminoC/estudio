// Script global para mantener el estado de autenticación en todas las páginas
document.addEventListener('DOMContentLoaded', function() {
    updateAuthButtonGlobal();
});

function updateAuthButtonGlobal() {
    const authBtn = document.getElementById('authBtn');
    if (!authBtn) return;
    
    const userData = localStorage.getItem('userData');
    if (userData) {
        const user = JSON.parse(userData);
        authBtn.textContent = `Cerrar Sesión (${user.nombre})`;
        authBtn.onclick = function(e) {
            e.preventDefault();
            logoutGlobal();
        };
    } else {
        authBtn.textContent = 'Iniciar Sesión';
        authBtn.onclick = function(e) {
            e.preventDefault();
            // Redirigir al index donde está el modal de login
            window.location.href = '/estudio/index.html';
        };
    }
}

function logoutGlobal() {
    localStorage.removeItem('userData');
    
    // Si está en página de admin, redirigir al inicio
    if (window.location.pathname.includes('admin')) {
        window.location.href = '/estudio/index.html';
    } else {
        // Actualizar el botón en la página actual
        updateAuthButtonGlobal();
    }
}