document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filter-form');
    const productArea = document.querySelector('.product-list');
    const statusMsg = document.getElementById('status-msg');
    const searchInput = document.getElementById('search-input');
    const availToggle = document.getElementById('avail-toggle');

    // Elementi per l'aggiornamento dinamico del prezzo
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');

    let debounceTimer;

    /**
     * Aggiorna il testo del prezzo mentre si sposta lo slider
     */
    if (priceRange && priceValue) {
        priceRange.addEventListener('input', () => {
            priceValue.textContent = `${priceRange.value}€`;
        });
    }

    /**
     * Collega gli eventi ai bottoni "Aggiungi al carrello/preferiti"
     * Necessario chiamarla ogni volta che il contenuto della lista cambia
     */
    const attachProductEvents = () => {
        document.querySelectorAll('.btn-add-cart').forEach(btn => {
            btn.onclick = () => addToCart(btn.dataset.id);
        });
        document.querySelectorAll('.btn-add-preferiti').forEach(btn => {
            btn.onclick = () => addToFavorites(btn.dataset.id);
        });
    };

    /**
     * Esegue la chiamata AJAX per filtrare i prodotti
     */
    /**
     * Esegue la chiamata AJAX con feedback di caricamento
     */
    const updateProducts = () => {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();

        // Feedback visivo: aggiungiamo una classe per opacizzare l'area
        productArea.classList.add('loading-fade');

        fetch(`shop.php?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newList = doc.querySelector('.product-list').innerHTML;
                const newStatus = doc.querySelector('#status-msg').textContent;

                productArea.innerHTML = newList;
                statusMsg.textContent = newStatus;

                // Rimuoviamo il feedback visivo
                productArea.classList.remove('loading-fade');

                attachProductEvents();
            })
            .catch(error => {
                console.error('Errore nel filtraggio:', error);
                productArea.classList.remove('loading-fade');
            });
    };

    // Listener per la ricerca con debounce (400ms)
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateProducts, 400);
        });
    }

    // Listener per Categorie, Range Prezzo e Checkbox Disponibilità
    filterForm.querySelectorAll('input[type="radio"], input[type="range"], input[type="checkbox"]')
        .forEach(input => {
            input.addEventListener('change', updateProducts);
        });

    // Gestione invio manuale del form
    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        updateProducts();
    });

    // Inizializzazione al primo caricamento
    attachProductEvents();
    updateCartBadge();
});

// --- FUNZIONI GLOBALI (CARRELLO E NOTIFICHE) ---

function addToCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            quantity: 1,
            addedAt: new Date().toISOString()
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    showNotification('Prodotto aggiunto al carrello!', 'success');
    updateCartBadge();
}

function addToFavorites(productId) {
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    if (favorites.includes(productId)) {
        showNotification('Questo prodotto è già nei tuoi preferiti!', 'info');
        return;
    }
    favorites.push(productId);
    localStorage.setItem('favorites', JSON.stringify(favorites));
    showNotification('Prodotto aggiunto ai preferiti!', 'success');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateCartBadge() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    let badge = document.querySelector('.cart-badge');
    const cartLink = document.querySelector('.cart-link');

    if (!cartLink) return;

    if (totalItems > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'cart-badge';
            cartLink.appendChild(badge);
        }
        badge.textContent = totalItems;
    } else if (badge) {
        badge.remove();
    }
}