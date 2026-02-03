
document.addEventListener('DOMContentLoaded', () => {

    /* PARALLASSE & HERO FADE */
    const layers = document.querySelectorAll('.parallax-layer');
    const heroSection = document.querySelector('.hero-section');
    const layerSpeeds = [0.85, 0.75, 0.65, 0.30, 0.35, 0.25, 0.15, 0.0];

    let ticking = false;

    function updateParallax() {
        const scrollY = window.scrollY;

        /* Se heroSection esiste e scrollY è maggiore dell'altezza di heroSection, allora ferma il parallax */
        if (heroSection && scrollY > heroSection.offsetHeight) {
            ticking = false;
            return;
        }

        /* Per ogni layer, applica una trasformazione translateY in base alla velocità */
        layers.forEach((layer, index) => {
            /* Gestisco solo le immagini esistenti. Se ce ne sono meno di 8, quelle non esistenti vengonno lasciate immobili */
            if (index < layerSpeeds.length) {
                const speed = layerSpeeds[index];
                if (layer.classList.contains('layer-4') || layer.classList.contains('layer-7')) {
                    layer.style.transform = `translateY(${scrollY * speed}px)`;
                } else {
                    /* Gli altri layer vengon ospostati anche lungo l'asse x per centrarli */
                    layer.style.transform = `translateX(-50%) translateY(${scrollY * speed}px)`;
                }
            }
        });

        ticking = false;
    }

    /* Quando avviene uno scroll, viene chiamata la funzione updateParallax e viene impostato ticking a true per dire che una animazione è già in corso */
    /* Se c'è già una animazione in corso, non viene chiamata la funzione updateParallax */
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }
    });
});