<?php
require_once '../config/check_auth.php';
requireAuth('login.html');
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
    <link rel="stylesheet" href="../style/style.css">
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
            <li><a href="shop.html"><span lang="en-GB">Shop</span></a></li>
            <li><a href="tea-info.html">Il nostro Tè</a></li>
            <li><a href="about.html"><span lang="en-GB">About</span></a></li>
        </ul>
    </nav>

    <nav id="user-actions" aria-label="Menu utente">
        <ul>
            <li><a href="cart.html" class="cart-link" aria-label="Visualizza carrello"><img
                            src="../../assets/images/shopping-cart.png" alt="Carrello" width="24" height="24"></a></li>
            <li><a href="login.html" class="btn-join" aria-label="Accedi al profilo">Join Now</a></li>
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
<footer>
    <p><a href="https://validator.w3.org/nu/"><img src="https://www.w3.org/Icons/valid-xhtml10" alt="HTML Valido!"></a>
    </p>
    <p>Copyright© 2025 by PCMS - <span lang="en-GB">All rights reserved</span>. Tutti i prodotti presentati sono frutto
        della nostra immaginazione e ogni riferimento a persone o cose realmente esistenti è casuale.</p>
    <ul>
        <li>Email: <a href="mailto:universitea@gmail.com">universitea@gmail.com</a></li>
        <li>Telefono: <a href="tel:+3912312390123">+39 12312390123</a></li>
    </ul>
    <p><a href="http://jigsaw.w3.org/css-validator/check/referer"><img
                    src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valido!"></a></p>
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

