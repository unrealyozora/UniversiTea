<?php
require_once '../config/check_auth.php';
requireAuth('login.php');
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Il tuo carrello</title>
    <meta name="description"
          content="Pagina dedicata al tuo carrello.">
    <meta name="keywords" content="tè, università, tisane, carrello , negozio">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" type="image/svg+xml" href="../../assets/images/logo-finestra.svg">
    <link rel="stylesheet" href="../style/style.css">

    <link rel="stylesheet" href="../style/print.css" media="print">
</head>
<body>
<header class="main-header">

    <div class="logo">
        <a href="../../index.html">
            <img src="../../assets/images/universitea_logo.svg" alt="UniversiTea - Torna alla Home" class="logo-img">
        </a>
    </div>

    <nav id="main-menu" aria-label="Menu principale">
        <div class="main-menu-pill"></div>
        <ul>
            <li><a href="shop.php"><span lang="en-GB">Shop</span></a></li>
            <li><a href="tea-info.html">Il nostro Tè</a></li>
            <li><a href="about.html"><span lang="en-GB">About</span></a></li>
            <li><a href="dashboard.php">Il tuo profilo</a></li>
        </ul>
    </nav>

    <nav id="user-actions" aria-label="Menu utente">
        <ul>
            <li><a href="cart.html" class="cart-link" aria-label="Visualizza carrello"><img
                            src="../../assets/images/shopping-cart.png" alt="Carrello" width="24" height="24"></a></li>
            <li><a href="templates/login.html" class="btn-join" aria-label="Accedi al profilo">Join Now</a></li>
        </ul>
    </nav>
</header>
<main>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Il Tuo Carrello</h1>
            <div class="cart-stats">
                <div>
                    <strong id="itemCount">0</strong> Prodotti
                </div>
                <div>
                    Totale: <strong id="totalAmount">€0.00</strong>
                </div>
            </div>
        </div>

        <div class="cart-content">
            <div class="alert" id="alertMessage"></div>

            <div id="cartContainer">
                <div class="loading">
                    <div class="spinner"></div>
                    Caricamento carrello...
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="main-footer-area">
    <div class="footer-container">

        <!-- Colonna Brand -->
        <div class="footer-column brand-col">
            <img src="../../assets/images/universitea_logo.svg" alt="UniversiTea" class="footer-logo">
            <p class="footer-tagline">
                Il tè che studia con te. Dalla natura incontaminata, direttamente nella tua tazza.
            </p>
            <div class="social-links">
                <a href="#" aria-label="Seguici su Facebook" class="social-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Seguici su Instagram" class="social-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="#" aria-label="Seguici su Twitter" class="social-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Mappa del Sito -->
        <nav class="footer-column" aria-label="Mappa del sito">
            <h3>Naviga</h3>
            <ul>
                <li><a href="../../index.html"><span lang="en-GB">Home</span></a></li>
                <li><a href="shop.php"><span lang="en-GB">Shop (Ti trovi qui ora)</span></a></li>
                <li><a href="tea-info.html">Il nostro Tè</a></li>
                <li><a href="about.html"><span lang="en-GB">About</span></a></li>
                <li><a href="dashboard.php">Il tuo profilo</a></li>
                <li><a href="preferiti.php">Preferiti</a></li>
                <li><a href="cart.php">Carrello (Ti trovi qui ora)</a></li>
                <li><a href="register.php">Registrati</a></li>
                <li><a href="templates/login.html">Accedi</a></li>
            </ul>
        </nav>

        <!-- Categorie Prodotti -->
        <nav class="footer-column" aria-label="Categorie prodotti">
            <h3>Prodotti</h3>
            <ul>
                <
                <li><a href="shop.php?category=bevande">Tè & Infusi</a></li>
                <li><a href="shop.php?category=merchandishing"><span lang="en-GB">Merch</span> & Accessori</a></li>
                <li><a href="shop.php?category=servizi">I Servizi</a></li>
                <li><a href="shop.php?category=bundle">I Bundle</a></li>
                <li><a href="shop.php?filter=bestseller">I più venduti</a></li>
                <li><a href="register.php">Programma Fedeltà</a></li>
            </ul>
        </nav>

        <!-- Contatti & Supporto -->
        <div class="footer-column">
            <h3>Contatti</h3>
            <ul class="contact-list">
                <li>
                    <strong>Email:</strong><br>
                    <a href="mailto:info@universitea.it">info@universitea.it</a>
                </li>
                <li>
                    <strong>Telefono:</strong><br>
                    <a href="tel:+3912312390123">+39 123 123 90123</a>
                </li>
                <li>
                    <strong>Orari Assistenza:</strong><br>
                    Lun-Ven: 9:00 - 18:00<br>
                    Sab: 10:00 - 16:00
                </li>
            </ul>
        </div>

    </div>

    <!-- Fascia Bottom -->
    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <div class="legal-info">
                <p><span lang="en-GB">Copyright</span> © 2025 <span lang="en-GB">UniversiTea</span> - <span
                            lang="en-GB">All rights reserved</span>.</p>
                <p class="disclaimer">Questo sito è un progetto accademico realizzato per scopi didattici.</p>
            </div>

            <div class="validators">
                <a href="https://validator.w3.org/nu/" target="_blank" rel="noopener noreferrer">
                    <img src="https://www.w3.org/Icons/valid-xhtml10" alt="HTML Valido" width="88" height="31">
                </a>
                <a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank" rel="noopener noreferrer">
                    <img src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valido" width="88"
                         height="31">
                </a>
            </div>
        </div>
    </div>
</footer>
</body>
<script>
    let cart = [];

    // Carica il carrello all'avvio
    document.addEventListener('DOMContentLoaded', () => {
        loadCart();
    });

    // Carica carrello dal server
    async function loadCart() {
        try {
            const response = await fetch('../config/cart_api/cart_get.php');
            const data = await response.json();

            if (data.success) {
                cart = data.cart;
                displayCart();
            } else {
                showAlert('Errore nel caricamento del carrello', 'error');
            }
        } catch (error) {
            console.error('Errore:', error);
            showAlert('Errore di connessione al server', 'error');
        }
    }

    // Mostra il carrello
    function displayCart() {
        const container = document.getElementById('cartContainer');
        const itemCount = document.getElementById('itemCount');
        const totalAmount = document.getElementById('totalAmount');

        if (cart.length === 0) {
            container.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <h2>Il tuo carrello è vuoto</h2>
                        <p>Aggiungi alcuni prodotti per iniziare!</p>
                        <a href="../../index.html" class="shop-btn">Vai allo Shop</a>
                    </div>
                `;
            itemCount.textContent = '0';
            totalAmount.textContent = '€0.00';
            return;
        }

        const totalItems = cart.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        const total = cart.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);

        itemCount.textContent = totalItems;
        totalAmount.textContent = '€' + total.toFixed(2);


        container.innerHTML = `
                <div class="cart-items">
                    ${cart.map(item => `
                        <div class="cart-item" id="item-${item.product_id}">
                            <div class="item-details">
                                <div class="item-name">${item.nome}</div>
                                <div class="item-price">€${parseFloat(item.prezzo).toFixed(2)} per unità</div>
                            </div>
                            <div class="item-actions">
                                <div class="item-subtotal">€${parseFloat(item.subtotal).toFixed(2)}</div>
                                <div class="quantity-controls">
                                    <button class="quantity-btn" onclick="updateQuantity(${item.product_id}, ${parseInt(item.quantity) - 1})"
                                            ${parseInt(item.quantity) <= 1 ? 'disabled' : ''}>-</button>
                                    <span class="quantity-display">${item.quantity}</span>
                                    <button class="quantity-btn" onclick="updateQuantity(${item.product_id}, ${parseInt(item.quantity) + 1})"
                                            ${parseInt(item.quantity) >= item.stock ? 'disabled' : ''}>+</button>
                                </div>
                                <button class="remove-btn" onclick="removeItem('${item.product_id}')">🗑️ Rimuovi</button>
                            </div>
                        </div>
                    `).join('')}
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotale (${totalItems} articoli):</span>
                        <span>€${total.toFixed(2)}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Totale:</span>
                        <span>€${total.toFixed(2)}</span>
                    </div>

                    <div class="checkout-actions">
                        <button class="clear-cart-btn" onclick="clearCart()">🗑️ Svuota Carrello</button>
                        <button class="checkout-btn" onclick="checkout()">💳 Procedi al Checkout</button>
                    </div>
                </div>
            `;
    }

    // Aggiorna quantità
    async function updateQuantity(productId, newQuantity) {
        if (newQuantity < 1) return;

        try {
            const response = await fetch('api/cart_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: newQuantity
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Quantità aggiornata!', 'success');
                await loadCart();
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Errore:', error);
            showAlert('Errore nell\'aggiornamento', 'error');
        }
    }

    // Rimuovi prodotto
    async function removeItem(productId) {
        if (!confirm('Vuoi rimuovere questo prodotto dal carrello?')) return;

        try {
            const response = await fetch('../config/cart_api/cart_remove.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId
                })
            });

            const data = await response.json();

            if (data.success) {
                showAlert('Prodotto rimosso dal carrello', 'success');
                await loadCart();
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Errore:', error);
            showAlert('Errore nella rimozione', 'error');
        }
    }

    // Svuota carrello
    async function clearCart() {
        if (!confirm('Vuoi svuotare completamente il carrello?')) return;

        try {
            // Rimuovi tutti i prodotti uno per uno
            for (const item of cart) {
                await fetch('api/cart_remove.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: item.product_id
                    })
                });
            }

            showAlert('Carrello svuotato!', 'success');
            await loadCart();
        } catch (error) {
            console.error('Errore:', error);
            showAlert('Errore nello svuotamento del carrello', 'error');
        }
    }

    // Checkout
    function checkout() {
        if (cart.length === 0) return;

        const total = cart.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
        const itemCount = cart.reduce((sum, item) => sum + parseInt(item.quantity), 0);

        const confirmMsg = `
Confermi l'ordine?

Articoli: ${itemCount}
Totale: €${total.toFixed(2)}

Procedi con il pagamento?
            `;

        if (confirm(confirmMsg)) {
            // Qui puoi reindirizzare a una pagina di checkout reale
            alert('🎉 Ordine confermato!\n\nGrazie per il tuo acquisto!\n\nVerrai reindirizzato alla pagina di conferma...');

            // Svuota il carrello dopo l'ordine
            clearCart().then(() => {
                // In un caso reale, qui reindirizzeresti a una pagina di conferma ordine
                // window.location.href = 'order_confirmation.php?order_id=123';
            });
        }
    }

    // Mostra alert
    function showAlert(message, type) {
        const alert = document.getElementById('alertMessage');
        alert.textContent = message;
        alert.className = `alert ${type}`;
        alert.classList.add('show');

        setTimeout(() => {
            alert.classList.remove('show');
        }, 3000);
    }
</script>

