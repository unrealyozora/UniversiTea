document.addEventListener('DOMContentLoaded', () => {
    // 1. Recupera ID dalla URL
    const params = new URLSearchParams(window.location.search);
    const productId = params.get('id');

    if (!productId) {
        window.location.href = 'shop.html';
        return;
    }

    fetchProductDetails(productId);
});

async function fetchProductDetails(id) {
    // Aggiungo timestamp per evitare cache browser
    const apiUrl = `../config/api_prodotti.php?id=${id}&t=${new Date().getTime()}`;

    try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error('Errore server');

        const data = await response.json();

        // Se l'array è vuoto
        if (data.length === 0) {
            document.querySelector('main').innerHTML = "<div class='section-dark' style='text-align:center; padding: 4em;'><h2>Prodotto non trovato</h2><a href='shop.php' class='btn-join'>Torna allo shop</a></div>";
            return;
        }

        renderProduct(data[0]);

    } catch (error) {
        console.error("Errore fetch:", error);
        document.querySelector('main').innerHTML = "<h2 style='text-align:center; padding:50px;'>Impossibile caricare il prodotto.</h2>";
    }
}

function renderProduct(product) {
    // 1. Titolo Pagina Browser
    document.title = `${product.nome} - UniversiTea`;

    // 2. Elementi DOM (Recuperati tramite ID)
    const titleEl = document.getElementById('product-title');
    const descEl = document.getElementById('product-desc');
    const priceEl = document.getElementById('product-price');
    const imgEl = document.getElementById('product-img-main');
    const catEl = document.getElementById('product-category');
    const breadcrumbCurrent = document.getElementById('breadcrumb-current');

    // 3. Assegnazione Sicura (Controlliamo se l'elemento esiste)
    if (titleEl) titleEl.textContent = product.nome;
    if (breadcrumbCurrent) breadcrumbCurrent.textContent = product.nome;
    if (descEl) descEl.textContent = product.descrizione;
    if (catEl) catEl.textContent = product.categoria;

    // 4. Prezzo formattato
    if (priceEl) {
        const prezzoF = parseFloat(product.prezzo).toFixed(2);
        priceEl.textContent = `€${prezzoF}`;
        // Accessibilità: Screen reader legge "Prezzo: X euro"
        priceEl.setAttribute('aria-label', `Prezzo: ${prezzoF} euro`);
    }

    // 5. Gestione Disponibilità
    const availContainer = document.getElementById('product-availability');
    // Nota: availContainer contiene <span>.text, lo cerchiamo al suo interno
    const availText = availContainer ? availContainer.querySelector('.text') : null;
    const btnAdd = document.querySelector('.btn-add-cart');

    if (availContainer && availText) {
        const isAvailable = parseInt(product.disponibilita) > 0;

        if (isAvailable) {
            availText.textContent = "Disponibile";
            availContainer.classList.remove('out-of-stock');
            availContainer.classList.add('available');
            if(btnAdd) {
                btnAdd.disabled = false;
                btnAdd.textContent = "Aggiungi al carrello";
            }
        } else {
            availText.textContent = "Non disponibile";
            availContainer.classList.remove('available');
            availContainer.classList.add('out-of-stock');
            if(btnAdd) {
                btnAdd.disabled = true;
                btnAdd.textContent = "Esaurito";
            }
        }
    }

    // 6. Immagine
    if (imgEl) {
        imgEl.src = getImagePlaceholder(product.categoria);
        imgEl.alt = `Dettaglio del prodotto: ${product.nome}`;
    }

    // 7. Specifiche Tecniche (Dinamiche)
    renderSpecs(product);
}

function renderSpecs(product) {
    // Cerchiamo il placeholder specifico
    const specsPlaceholder = document.getElementById('specs-placeholder');
    if (!specsPlaceholder) return; // Se non c'è, usciamo senza errori

    let specsHtml = '';

    switch (product.categoria) {
        case 'bevande':
            if (product.temp_consigliata) {
                specsHtml += `<div class="spec-item"><strong>Temp. Acqua:</strong> ${product.temp_consigliata}°C</div>`;
            }
            if (product.tipologia_bevanda) {
                specsHtml += `<div class="spec-item"><strong>Tipologia:</strong> ${product.tipologia_bevanda}</div>`;
            }
            break;

        case 'merchandising':
            if (product.Materiale) {
                specsHtml += `<div class="spec-item"><strong>Materiale:</strong> ${product.Materiale}</div>`;
            }
            break;

        case 'servizi':
            if (product.livello_urgenza) {
                specsHtml += `<div class="spec-item"><strong>Urgenza:</strong> ${product.livello_urgenza}</div>`;
            }
            break;
    }

    if (specsHtml) {
        // Creiamo il div wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'product-specs';
        wrapper.innerHTML = `<h3>Scheda Tecnica</h3>` + specsHtml;

        // Puliamo e inseriamo
        specsPlaceholder.innerHTML = '';
        specsPlaceholder.appendChild(wrapper);
    }
}

// Funzione Helper Immagini
function getImagePlaceholder(categoria) {
    const basePath = '../../assets/images/';
    switch (categoria) {
        case 'bevande': return basePath + 'placeholder_tea.svg';
        case 'merchandising': return basePath + 'placeholder_merch.jpg';
        case 'servizi': return basePath + 'placeholder_service.svg';
        default: return basePath + 'placeholder_generic.jpg';
    }
}