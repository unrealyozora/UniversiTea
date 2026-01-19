
    document.addEventListener('DOMContentLoaded', () => {

        /* --- SCRIPT HEADER/PARALLASSE (Menu Logic rimossa, ora è solo CSS) --- */
        const header = document.querySelector('.main-header');

        /* 1. HEADER COLOR CHANGE */
        function updateHeaderColor() {
            const triggerPoint = window.innerHeight * 0.85;
            if (window.scrollY > triggerPoint) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
        window.addEventListener('scroll', updateHeaderColor);


        /* 2. PARALLASSE & HERO FADE */
        const layers = document.querySelectorAll('.parallax-layer');
        const heroSection = document.querySelector('.hero-section');
        const heroContent = document.querySelector('.hero-text-box');
        const layerSpeeds = [0.85, 0.75, 0.65, 0.30, 0.35, 0.25, 0.15, 0.0];
        const fadeDivisor = window.innerHeight / 1.5;

        let ticking = false;

        function updateParallax() {
            const scrollY = window.scrollY;

            if (heroSection && scrollY > heroSection.offsetHeight) {
                ticking = false;
                return;
            }

            layers.forEach((layer, index) => {
                if (index < layerSpeeds.length) {
                    const speed = layerSpeeds[index];
                    if (layer.classList.contains('layer-4') || layer.classList.contains('layer-7')) {
                        layer.style.transform = `translateY(${scrollY * speed}px)`;
                    } else {
                        layer.style.transform = `translateX(-50%) translateY(${scrollY * speed}px)`;
                    }
                }
            });

            if (heroContent) {
                if (scrollY < 10) {
                    heroContent.style.opacity = '';
                    heroContent.style.filter = '';
                }
                else {
                    const opacity = 1 - (scrollY / fadeDivisor);
                    heroContent.style.opacity = opacity > 0 ? opacity : 0;
                }
            }
            ticking = false;
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        });
    });