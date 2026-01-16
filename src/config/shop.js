document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filter-form');
    const productArea = document.querySelector('.product-list');
    const statusMsg = document.getElementById('status-msg');
    const searchInput = document.getElementById('search-input');

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
     * Collega gli eventi SOLO ai bottoni "Preferiti"
     * (Il carrello è ora gestito dal form HTML nativo per comunicare con PHP)
     */
    const attachProductEvents = () => {
        // Gestione Preferiti (rimane in JS/LocalStorage per ora)
        document.querySelectorAll('.btn-add-preferiti').forEach(btn => {
            // Rimuoviamo eventuali listener precedenti per evitare duplicati
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);

            newBtn.onclick = (e) => {
                e.preventDefault(); // Evita scroll o reload
                addToFavorites(newBtn.dataset.id);
            };
        });
    };

    /**
     * Esegue la chiamata AJAX per filtrare i prodotti
     * Recupera l'intero HTML di shop.php e ne estrae solo la lista
     */
    const updateProducts = () => {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();

        // Feedback visivo: opacità durante il caricamento
        if(productArea) productArea.classList.add('loading-fade');

        fetch(`shop.php?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Estrai la nuova lista prodotti e il messaggio di stato
                const newList = doc.querySelector('.product-list')?.innerHTML;
                const newStatus = doc.querySelector('#status-msg')?.textContent;

                if (productArea && newList) productArea.innerHTML = newList;
                if (statusMsg && newStatus) statusMsg.textContent = newStatus;

                // Rimuoviamo il feedback visivo
                if(productArea) productArea.classList.remove('loading-fade');

                // Ricollega gli eventi ai nuovi elementi caricati
                attachProductEvents();
            })
            .catch(error => {
                console.error('Errore nel filtraggio:', error);
                if(productArea) productArea.classList.remove('loading-fade');
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
    if (filterForm) {
        filterForm.querySelectorAll('input[type="radio"], input[type="range"], input[type="checkbox"]')
            .forEach(input => {
                input.addEventListener('change', updateProducts);
            });

        // Gestione invio manuale del form (es. se si preme invio nella search bar)
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            updateProducts();
        });
    }

    // Inizializzazione al primo caricamento
    attachProductEvents();

    // Gestione dissolvenza messaggi PHP (Successo/Errore aggiunta carrello)
    const phpFeedback = document.querySelector('.success-msg, .error-msg');
    if (phpFeedback) {
        setTimeout(() => {
            phpFeedback.style.transition = "opacity 0.5s ease";
            phpFeedback.style.opacity = "0";
            setTimeout(() => phpFeedback.remove(), 500);
        }, 4000);
    }
});

// --- FUNZIONI GLOBALI (PREFERITI E NOTIFICHE JS) ---

function addToFavorites(productId) {
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

    // Toggle (Aggiungi/Rimuovi)
    if (favorites.includes(productId)) {
        favorites = favorites.filter(id => id !== productId);
        showNotification('Prodotto rimosso dai preferiti', 'info');
    } else {
        favorites.push(productId);
        showNotification('Prodotto aggiunto ai preferiti! ❤', 'success');
    }

    localStorage.setItem('favorites', JSON.stringify(favorites));

    // Qui potresti aggiungere logica per cambiare l'icona del cuore (pieno/vuoto)
}

function showNotification(message, type = 'info') {
    // Rimuovi notifiche esistenti per non sovrapporle
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    // Animazione entrata/uscita
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}