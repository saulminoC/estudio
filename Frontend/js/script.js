// Mostrar el modal de inicio de sesión al hacer clic en el botón de iniciar sesión
document.querySelector('.btn-secondary').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('loginModal').style.display = 'block'; // Muestra el modal de login
});

// Función para cerrar los modales
document.querySelectorAll('.close-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('registerModal').style.display = 'none';
    });
});

// Cerrar los modales si el usuario hace clic fuera de ellos
window.addEventListener('click', function(e) {
    if (e.target == document.getElementById('loginModal') || e.target == document.getElementById('registerModal')) {
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('registerModal').style.display = 'none';
    }
});

// Mostrar el formulario de registro desde el modal de login
document.getElementById('show-register').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('loginModal').style.display = 'none';
    document.getElementById('registerModal').style.display = 'block';
});

// Mostrar el formulario de inicio de sesión desde el modal de registro
document.getElementById('show-login').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('registerModal').style.display = 'none';
    document.getElementById('loginModal').style.display = 'block';
});

// Smooth scrolling para navegación
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Animaciones al hacer scroll (agregar la clase 'visible' cuando el elemento entra en vista)
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
});

// Chat widget functionality (simula un chat en vivo)
document.querySelector('.chat-widget').addEventListener('click', function() {
    alert('¡Hola! El chat en vivo estará disponible próximamente. Por ahora puedes contactarnos al +52 222 123 4567 o info@estudiofotografico.com');
});

// Cambio de fondo del encabezado al hacer scroll
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (window.scrollY > 100) {
        header.style.background = 'rgba(26, 26, 26, 0.95)';
    } else {
        header.style.background = 'linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%)';
    }
});

// Efecto al pasar el cursor sobre los elementos del portafolio
document.querySelectorAll('.portfolio-item').forEach(item => {
    item.addEventListener('click', function() {
        const category = this.querySelector('.portfolio-overlay div').textContent.trim();
        alert(`Próximamente: Galería de ${category}`);
    });
});

// Funcionalidad para los botones de los servicios
document.querySelectorAll('.service-card .btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const serviceName = this.closest('.service-card').querySelector('h3').textContent;
        alert(`Próximamente: Paquetes de ${serviceName}`);
    });
});

// Funcionalidad para los botones de CTA (llamadas a la acción)
document.querySelectorAll('.cta .btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        if(this.textContent.includes('Reservar')) {
            alert('Próximamente: Sistema de reservas en línea');
        } else {
            alert('Próximamente: Formulario de contacto');
        }
    });
});

// Funcionalidad de los botones en la sección Hero (ver portafolio, servicios)
document.querySelectorAll('.hero-buttons .btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const href = this.getAttribute('href'); // Obtener el atributo href
        if(href && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if(target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        } else {
            // Si no es un enlace válido, se podría manejar algún tipo de fallback
            alert('Funcionalidad pendiente');
        }
    });
});
