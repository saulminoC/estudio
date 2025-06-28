// Efecto scroll suave para los enlaces del menú
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Cambio de estilo del header al hacer scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.style.background = 'var(--primary)';
                header.style.padding = '0.5rem 5%';
            } else {
                header.style.background = 'rgba(51, 48, 61, 0.9)';
                header.style.padding = '1rem 5%';
            }
        });

        // Simulador de slider de testimonios
        let currentTestimonial = 0;
        const testimonials = [
            {
                content: "Las fotografías de nuestra boda superaron todas nuestras expectativas. Studio Lens capturó cada momento especial con tanta emoción y belleza que cada vez que vemos las fotos parece que estamos reviviendo ese día.",
                author: "- Laura y Miguel"
            },
            {
                content: "Increíble trabajo con nuestras fotos familiares. Lograron capturar exactamente la personalidad de cada miembro de la familia y la conexión entre nosotros. Recomiendo Studio Lens 100%.",
                author: "- Familia Rodríguez"
            },
            {
                content: "Necesitaba fotos profesionales para mi marca personal y el resultado fue excelente. Me sentí muy cómoda durante la sesión y las fotos han tenido un gran impacto en mi presencia profesional en línea.",
                author: "- Dra. Ana Martínez"
            }
        ];

        function changeTestimonial() {
            const testimonialContainer = document.querySelector('.testimonial');
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            
            testimonialContainer.innerHTML = `
                <div class="testimonial-content">
                    "${testimonials[currentTestimonial].content}"
                </div>
                <div class="testimonial-author">${testimonials[currentTestimonial].author}</div>
            `;
        }

        // Cambiar testimonial cada 6 segundos
        setInterval(changeTestimonial, 6000);