
document.addEventListener('DOMContentLoaded', () => {

    /* PARALLASSE & HERO FADE */
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
            heroContent.style.opacity = '1';
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