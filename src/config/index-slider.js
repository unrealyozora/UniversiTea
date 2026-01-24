// --- SLIDER SCRIPT (Invariato) ---
const teaList = [
    {
        name: "Tè Verde Matcha",
        desc: "Non un semplice tè, ma un antico rituale. Ottenuto dalla macinatura a pietra delle foglie più pregiate coltivate in ombra, questo \"oro verde\" offre un concentrato ineguagliabile di antiossidanti. Il suo sapore è un perfetto equilibrio tra dolcezza vegetale e note umami.",
        model: "assets/images/Mate.glb"
    },
    {
        name: "Earl Grey Imperiale",
        desc: "Il classico tè nero aromatizzato con vero olio essenziale di bergamotto calabrese. Un blend aristocratico e profumato, perfetto per il pomeriggio, capace di trasformare ogni pausa in un momento di pura eleganza britannica.",
        model: "assets/images/tea_container.glb"
    },
    {
        name: "Infuso ai Frutti Rossi",
        desc: "Una miscela dolce e dissetante, totalmente priva di teina. L'esplosione di fragole, lamponi e karkadè la rende perfetta sia come bevanda calda rinvigorente d'inverno, sia come dissetante tè freddo d'estate.",
        model: "assets/images/bustina.glb"
    }
];

let currentIndex = 0;
let isAnimating = false;
const ANIMATION_DURATION = 700;

const wrapper = document.querySelector('.bestsellers-content-wrapper');
const productName = document.querySelector('.bestsellers-product-name');
const productDesc = document.querySelector('.bestsellers-info p');
const modelViewer = document.querySelector('model-viewer');
const btnPrev = document.querySelector('.bestsellers-nav-btn.prev');
const btnNext = document.querySelector('.bestsellers-nav-btn.next');
const animCheckbox = document.getElementById('disable-anim');
const rotCheckbox = document.getElementById('disable-rot');

function toggleButtons(enable) {
    if (enable) {
        btnPrev.disabled = false;
        btnNext.disabled = false;
        isAnimating = false;
    } else {
        btnPrev.disabled = true;
        btnNext.disabled = true;
        isAnimating = true;
    }
}

function init() {
    wrapper.setAttribute('data-direction', 'next');

    // GENERAZIONE PALLINI (Semantica: DIV > SPAN generati via JS)
    const header = document.querySelector('.bestsellers-header');

    // 1. Crea contenitore
    const dotsBox = document.createElement('div');
    dotsBox.id = 'bestsellers-dots';
    dotsBox.className = 'bestsellers-dots-container';
    dotsBox.setAttribute('aria-hidden', 'true'); // Nascosto a Screen Reader (decorativo)

    // 2. Crea pallini
    teaList.forEach((_, index) => {
        const dot = document.createElement('span'); // SPAN generico per decorazione
        dot.classList.add('bestsellers-dot');
        if (index === currentIndex) dot.classList.add('active');
        dotsBox.appendChild(dot);
    });

    // 3. Appendi nel posto giusto (dopo i pulsanti di navigazione, o centrato via CSS)
    // Nota: Nel CSS il container è absolute (left: 50%), quindi basta appenderlo nell'header relativo
    header.appendChild(dotsBox);

    btnPrev.addEventListener('click', () => changeTea(-1));
    btnNext.addEventListener('click', () => changeTea(1));

    if (rotCheckbox) {
        rotCheckbox.addEventListener('change', (e) => {
            if (e.target.checked) modelViewer.removeAttribute('auto-rotate');
            else modelViewer.setAttribute('auto-rotate', '');
        });
    }
}

function changeTea(direction) {
    if (isAnimating) return;

    const reduceMotion = animCheckbox && animCheckbox.checked;

    let nextIndex = currentIndex + direction;
    if (nextIndex >= teaList.length) nextIndex = 0;
    else if (nextIndex < 0) nextIndex = teaList.length - 1;

    if (reduceMotion) {
        updateContent(nextIndex);
        return;
    }

    toggleButtons(false);

    wrapper.classList.add('no-transition');
    wrapper.classList.remove('animating-in', 'animating-out');

    const dirString = direction === 1 ? 'next' : 'prev';
    wrapper.setAttribute('data-direction', dirString);

    void wrapper.offsetWidth;

    wrapper.classList.remove('no-transition');

    requestAnimationFrame(() => {
        wrapper.classList.add('animating-in');
    });

    setTimeout(() => {
        updateContent(nextIndex);

        wrapper.classList.remove('animating-in');
        wrapper.classList.add('animating-out');

        setTimeout(() => {
            wrapper.classList.add('no-transition');
            wrapper.classList.remove('animating-out');
            void wrapper.offsetWidth;
            wrapper.classList.remove('no-transition');
            toggleButtons(true);
        }, ANIMATION_DURATION);

    }, ANIMATION_DURATION);
}

function updateContent(index) {
    currentIndex = index;
    const data = teaList[index];

    if (productName) productName.textContent = data.name;
    if (productDesc) productDesc.textContent = data.desc;
    if (modelViewer) modelViewer.setAttribute('src', data.model);

    const dots = document.querySelectorAll('.bestsellers-dot');
    dots.forEach((dot, i) => {
        if (i === index) dot.classList.add('active');
        else dot.classList.remove('active');
    });
}

document.addEventListener('DOMContentLoaded', init);