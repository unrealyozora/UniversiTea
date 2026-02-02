/**
 * PROGRESSIVE ENHANCEMENT
 * Intercetta i form standard. Converte i dati in JSON per le tue API.
 */
async function handleCartAction(event, form) {
    event.preventDefault(); // Blocca il submit classico

    // 1. Estrai dati dal form
    const formData = new FormData(form);
    const action = formData.get('action');
    const productId = formData.get('product_id');
    const quantity = formData.get('quantity');

    // 2. Conferma per azioni distruttive
    if (action === 'remove' && !confirm('Vuoi rimuovere il prodotto?')) return false;
    if (action === 'clear' && !confirm('Vuoi svuotare il carrello?')) return false;

    // 3. Determina URL API e Payload JSON
    // IMPORTANTE: Assicurati che questi percorsi puntino dove hai salvato i file API
    let url = '';
    let payload = {};

    if (action === 'remove') {
        url = '../config/cart_api/cart_remove.php'; // Adatta il percorso al tuo file
        payload = {product_id: productId};
    } else if (action === 'update_quantity') {
        url = '../config/cart_api/cart_update.php'; // Devi creare questo file! (Vedi punto 4 sotto)
        payload = {product_id: productId, quantity: quantity};
    } else if (action === 'clear') {
        url = '../config/cart_api/cart_clear.php';
        payload = {};
    }

    try {
        const response = await fetch(url, {
            method: 'POST', // Le tue API accettano POST/DELETE
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        // Gestione risposta
        // Alcune tue API potrebbero non ritornare JSON valido in caso di errore PHP, quindi usiamo try/catch
        const data = await response.json();

        if (data.success || response.ok) {
            // Successo: Ricarica pagina per aggiornare totali (o aggiorna DOM se vuoi farlo complesso)
            window.location.reload();
        } else {
            alert('Errore: ' + (data.message || 'Operazione fallita'));
        }
    } catch (error) {
        console.error('Errore JS:', error);
        // Fallback: Se JS fallisce, sottometti il form normalmente
        form.submit();
    }
    return false;
}