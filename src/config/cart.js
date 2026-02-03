/**
 * PROGRESSIVE ENHANCEMENT
 */
document.addEventListener("DOMContentLoaded", function () {
    initCartActions();
})

function initCartActions() {
    const cartForms = document.querySelectorAll('[data-cart-action]');
    cartForms.forEach((form) => {
        form.addEventListener('submit', handleCartAction);
    });
}

async function handleCartAction(event) {
    event.preventDefault(); // Blocca il submit classico
    const form = event.currentTarget;

    // 1. Estrai dati dal form
    const formData = new FormData(form);
    const action = formData.get('action');
    const productId = formData.get('product_id');
    const quantity = formData.get('quantity');

    // 2. Conferma per azioni distruttive
    if (action === 'remove' && !confirm('Vuoi rimuovere il prodotto?')) return false;
    if (action === 'clear' && !confirm('Vuoi svuotare il carrello?')) return false;


    let url = '';
    let payload = {};

    if (action === 'remove') {
        console.log("prova");
        url = '../config/cart_api/cart_remove.php'; //
        payload = {product_id: productId};
    } else if (action === 'update_quantity') {
        url = '../config/cart_api/cart_update.php'; //
        payload = {product_id: productId, quantity: quantity};
    } else if (action === 'clear') {
        url = '../config/cart_api/cart_clear.php';
        payload = {};
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        // Gestione risposta
        const data = await response.json();

        if (data.success || response.ok) {
            window.location.reload();
        } else {
            alert('Errore: ' + (data.message || 'Operazione fallita'));
        }
    } catch (error) {
        console.error('Errore JS:', error);
        form.submit();
    }
    return false;
}