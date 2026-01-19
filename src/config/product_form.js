function toggleFields() {
    // 1. Nascondi tutti i campi specifici
    document.querySelectorAll('.specific-fields').forEach(el => el.style.display = 'none');

    // 2. Prendi il valore selezionato
    const cat = document.getElementById('categoria').value;

    // 3. Mostra il div corrispondente (es. fields-bevande)
    const target = document.getElementById('fields-' + cat);
    if (target) {
        target.style.display = 'block';
    }
}

// Esegui al caricamento per mostrare i campi se siamo in modifica
document.addEventListener('DOMContentLoaded', toggleFields);