const API_URL = '../config/api_prodotti.php';
let currentProducts = []; // Qui salviamo i prodotti scaricati dal server

document.addEventListener('DOMContentLoaded', () => {
    // 1. Carica tutti i prodotti all'avvio
    fetchProducts();

    // 2. Gestione Filtri Categoria (Radio Buttons)
    const radioButtons = document.querySelectorAll('input[name="category"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', (e) => {
            const selectedCategory = e.target.value;
            fetchProducts(selectedCategory); // Ricarica dal server se cambia la macro-categoria
        });
    });

    // 3. Gestione Filtri Locali (Ricerca, Prezzo, Disponibilità)
    const searchInput = document.getElementById('search-input');
    const priceRange = document.getElementById('price-range');
    const availToggle = document.getElementById('avail-toggle');
    const filterForm = document.getElementById('filter-form');

    // Evita il reload del form al submit
    if (filterForm) {
        filterForm.addEventListener('submit', (e) => e.preventDefault());
    }

    // Aggiungiamo i listener per aggiornare la lista in tempo reale
    if (searchInput) searchInput.addEventListener('input', filterProducts);
    if (availToggle) availToggle.addEventListener('change', filterProducts);

    if (priceRange) {
        priceRange.addEventListener('input', (e) => {
            // Aggiorna visivamente il prezzo accanto allo slider
            const priceValue = document.getElementById('price-value');
            if (priceValue) priceValue.textContent = e.target.value + '€';
            // Applica il filtro
            filterProducts();
        });
    }
});

/**
 * Scarica i prodotti dal server (PHP)
 */
async function fetchProducts(category = 'tutti') {
    const listContainer = document.getElementById('product-list');
    const statusMsg = document.getElementById('status-msg');

    if (!listContainer || !statusMsg) return;

    statusMsg.textContent = "Caricamento prodotti in corso...";
    listContainer.innerHTML = ''; // Pulisce la lista mentre carica

    try {
        const url = category === 'tutti' ? API_URL : `${API_URL}?cat=${category}`;
        const response = await fetch(url);

        if (!response.ok) throw new Error('Errore nella risposta del server');

        // Salviamo i dati nella variabile globale
        currentProducts = await response.json();

        // Applichiamo i filtri (che all'inizio mostreranno tutto) e renderizziamo
        filterProducts();

    } catch (error) {
        console.error(error);
        statusMsg.textContent = "Errore nel caricamento dei prodotti. Riprova più tardi.";
    }
}

/**
 * Filtra i prodotti salvati in 'currentProducts' basandosi sugli input HTML
 * e poi chiama renderProducts
 */
function filterProducts() {
    // 1. Recupera i valori attuali degli input
    const searchText = document.getElementById('search-input')?.value.toLowerCase() || '';
    const maxPrice = parseFloat(document.getElementById('price-range')?.value) || 100;
    const onlyAvailable = document.getElementById('avail-toggle')?.checked || false;

    // 2. Filtra l'array globale
    const filtered = currentProducts.filter(product => {
        // Filtro Prezzo
        const price = parseFloat(product.prezzo);
        if (price > maxPrice) return false;

        // Filtro Disponibilità (Assume che disponibilità sia > 0 o '1')
        // Nota: Verifica come il DB restituisce la disponibilità (int o stringa)
        const stock = parseInt(product.disponibilita);

        // Se l'utente vuole solo disponibili e lo stock è 0 o meno, nascondi
        if (onlyAvailable && stock <= 0) return false;
        // Filtro Ricerca (Nome o Descrizione)
        const nameMatch = product.nome.toLowerCase().includes(searchText);
        const descMatch = product.descrizione ? product.descrizione.toLowerCase().includes(searchText) : false;

        return nameMatch || descMatch;
    });

    // 3. Disegna i risultati filtrati
    renderProducts(filtered);
}

/**
 * Genera l'HTML per la lista di prodotti fornita
 */
function renderProducts(products) {
    const listContainer = document.getElementById('product-list');
    const statusMsg = document.getElementById('status-msg');

    if (!listContainer) return;

    listContainer.innerHTML = '';

    if (products.length === 0) {
        statusMsg.textContent = "Nessun prodotto trovato con questi filtri.";
        listContainer.innerHTML = '<li class="no-results">Nessun risultato. Prova ad allargare la ricerca.</li>';
        return;
    }

    statusMsg.textContent = `Visualizzati ${products.length} prodotti.`;

    const productsHtml = products.map(product => {
        const nome = escapeHtml(product.nome);
        const desc = escapeHtml(product.descrizione);
        const prezzo = parseFloat(product.prezzo).toFixed(2);
        const id = product.id;
        const imgPath = getImagePlaceholder(product.categoria);

        return `
        <li class="product-item">
            <article class="product-card">
            <a aria-label="vai alla pagina di ${nome}" href="product.html?id=${id}">
                <img src="${imgPath}" alt="" loading="lazy">
            </a>
                <div class="product-info">
                    <h3>${nome}</h3>
                    <p class="category-tag">${product.categoria}</p>
                    <p class="description">${desc}</p>
                    <p class="price">€ ${prezzo}</p>
                    <button class="btn-add-cart" 
                            data-id="${id}"
                            aria-label="Aggiungi ${nome} al carrello">
                        Aggiungi al carrello
                    </button>
                    <button class="btn-add-preferiti" 
                            data-id="${id}"
                            aria-label="Aggiungi ${nome} ai Preferiti">
                        Aggiungi ai Preferiti
                    </button>
                </div>
            </article>
        </li>
        `;
    }).join('');

    listContainer.innerHTML = productsHtml;
}

// Funzione helper per le immagini
function getImagePlaceholder(categoria) {
    const basePath = '../../assets/images/';
    switch (categoria) {
        case 'bevande': return basePath + 'placeholder_tea.svg';
        case 'merchandising': return basePath + 'placeholder_merch.jpg';
        case 'servizi': return basePath + 'placeholder_service.svg';
        default: return basePath + 'placeholder_generic.jpg';
    }
}

// Funzione helper per sicurezza XSS
function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}