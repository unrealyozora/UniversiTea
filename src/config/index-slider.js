document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('js-carousel-active');

    const track = document.getElementById('bestsellers-content');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const dotsContainer = document.querySelector('.carousel-dots');
    const slides = document.querySelectorAll('.product-slide');
    const rotCheck = document.getElementById('disable-rot');

    if (!track || !slides.length) return;

    slides.forEach((_, index) => {
        const dot = document.createElement('button');
        dot.classList.add('carousel-dot');
        dot.ariaLabel = `Vai alla slide ${index + 1}`;
        if (index === 0) dot.classList.add('active');

        dot.addEventListener('click', () => {
            const targetSlide = slides[index];
            targetSlide.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' }); /* Scrolla fino a che l'elemento non è visibile. Scrolla con quelle proprietà */
        });

        dotsContainer.appendChild(dot);
    });

    const dots = document.querySelectorAll('.carousel-dot');

    const getCurrentSlideIndex = () => {
        const scrollLeft = track.scrollLeft; /* Calcolo della distanza percorsa dal bordo sinistro della prima slide */
        const slideWidth = slides[0].offsetWidth; /* Calcolo della larghezza di una slide */
        return Math.round(scrollLeft / slideWidth); /* calcolo indice della slide corrente. Sarebbe "largezza slide" / "distanza percorsa" */
    };

    nextBtn.addEventListener('click', () => {
        const currentIndex = getCurrentSlideIndex();
        const nextIndex = Math.min(currentIndex + 1, slides.length - 1); /* Calcolo della prossima slide. Sarebbe il minore tra "indice slide corrente + 1" e "numero di slide - 1". Serve per evitare di andare oltre l'ultima slide */
        slides[nextIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
    });

    prevBtn.addEventListener('click', () => {
        const currentIndex = getCurrentSlideIndex();
        const prevIndex = Math.max(currentIndex - 1, 0); /* Calcolo della slide precedente. Sarebbe il massimo tra "indice slide corrente - 1" e "0". Serve per evitare di andare oltre la prima slide */
        slides[prevIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
    });

    const updateActiveDot = () => {
        const index = getCurrentSlideIndex();

        dots.forEach(dot => dot.classList.remove('active'));

        if (dots[index]) {
            dots[index].classList.add('active');
        }

        /* Questo mi serve per far ruotare solo il modello che sto visualizzando. Per risparmiare risorse */
        if (rotCheck && !rotCheck.checked) {
            slides.forEach((slide, slideIndex) => { /* Prendo l'elemento slides e lo chiamo slide con posizione slideIndex */
                /* Prendo solo il model-viwer della slide che vedo, se esiste ed è quello che sto vedendo gli metto auto-rotate, agli altri tolgo quell'attributo */
                const viewer = slide.querySelector('model-viewer');
                if (viewer) {
                    if (slideIndex === index) {
                        viewer.setAttribute('auto-rotate', '');
                    } else {
                        viewer.removeAttribute('auto-rotate');
                    }
                }
            });
        }
    };

    track.addEventListener('scroll', updateActiveDot); /* Ad ogni scroll sul contenitore de prduct-slide avvio la funzione di aggiornamento pallini e controllo rotazione */

    if (rotCheck) {
        rotCheck.addEventListener('change', (e) => {
            const viewers = document.querySelectorAll('model-viewer');
            if (e.target.checked) { /* e.target è la mia checkbox, se questa è checked tolgo la rotazione a tutti*/
                viewers.forEach(v => v.removeAttribute('auto-rotate'));
            } else {
                const currentIndex = getCurrentSlideIndex();
                const currentViewer = slides[currentIndex].querySelector('model-viewer');
                if (currentViewer) {
                    currentViewer.setAttribute('auto-rotate', '');
                }
            }
        });
    }
});