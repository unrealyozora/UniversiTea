// shop.js

const API_URL = '../config/api_prodotti.php';
// NOTA: Assicurati che api_prodotti.php sia nella stessa cartella di shop.html,
// altrimenti aggiusta il percorso (es: '../api/api_prodotti.php')

document.addEventListener('DOMContentLoaded', () => {
    // Carica tutti i prodotti all'avvio
    fetchProducts();

    const filterForm = document.getElementById('filter-form');

    // 1. Selezioniamo tutti i radio button che hanno name="category"
    const radioButtons = document.querySelectorAll('input[name="category"]');

    // 2. Aggiungiamo un "orecchio" (listener) a ciascuno di essi
    radioButtons.forEach(radio => {
        radio.addEventListener('change', (e) => {
            // Appena un radio cambia stato (viene selezionato), prendiamo il suo valore
            const selectedCategory = e.target.value;

            // E ricarichiamo subito i prodotti
            fetchProducts(selectedCategory);
        });
    });

    // 3. (Opzionale) Se il form esiste, evitiamo che faccia il reload se uno preme invio per sbaglio
    if (filterForm) {
        filterForm.addEventListener('submit', (e) => e.preventDefault());
    }
});

async function fetchProducts(category = 'tutti') {
    const listContainer = document.getElementById('product-list');
    const statusMsg = document.getElementById('status-msg');

    // Controllo di sicurezza: se l'HTML non è aggiornato, evita errori in console
    if (!listContainer || !statusMsg) {
        console.error("Errore: Elementi DOM 'product-list' o 'status-msg' non trovati.");
        return;
    }

    statusMsg.textContent = "Caricamento prodotti in corso...";
    listContainer.innerHTML = '';

    try {
        const url = category === 'tutti' ? API_URL : `${API_URL}?cat=${category}`;
        const response = await fetch(url);

        if (!response.ok) throw new Error('Errore nella risposta del server');

        const products = await response.json();

        if (products.length === 0) {
            statusMsg.textContent = "Nessun prodotto trovato per questa categoria.";
            listContainer.innerHTML = '<li class="no-results">Nessun prodotto disponibile.</li>';
            listContainer.innerHTML = '<li class="no-results">Prova a selezionare un\'altra categoria.</li>';
            return;
        }

        statusMsg.textContent = `Trovati ${products.length} prodotti.`;

        const productsHtml = products.map(product => {
            const nome = escapeHtml(product.nome);
            const desc = escapeHtml(product.descrizione);
            const prezzo = parseFloat(product.prezzo).toFixed(2);
            const id = product.id;

            const imgPath = getImagePlaceholder(product.categoria);

            return `
            <li class="product-item">
                <article class="product-card">
                    <img src="${imgPath}" alt="Immagine di ${nome}" loading="lazy">
                    <div class="product-info">
                        <h3>${nome}</h3>
                        <p class="category-tag">${product.categoria}</p>
                        <p class="description">${desc}</p>
                        <p class="price">€ ${prezzo}</p>
                        <button class="btn-add" 
                                data-id="${id}"
                                aria-label="Aggiungi ${nome} al carrello">
                            Aggiungi al carrello
                        </button>
                    </div>
                </article>
            </li>
            `;
        }).join('');

        listContainer.innerHTML = productsHtml;

    } catch (error) {
        console.error(error);
        statusMsg.textContent = "Accidenti, nel retrobottega hanno rovesciato del té, prova a ricaricare la pagina e se l'errore persiste contattaci.";
    }
}

// Funzione helper per le immagini mancanti
function getImagePlaceholder(categoria) {
    const basePath = '../../assets/images/';
    switch (categoria) {
        case 'bevande': return basePath + 'placeholder_tea.svg';
        case 'merchandising': return basePath + 'placeholder_merch.jpg';
        case 'servizi': return basePath + 'placeholder_service.svg';
        default: return basePath + 'placeholder_generic.jpg';
    }
}

function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}