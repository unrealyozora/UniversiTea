function toggleFields() {
    // Nascondi tutti i campi specifici
    document.querySelectorAll('.specific-fields').forEach(el => el.style.display = 'none');

    // Recupera il valore selezionato
    const cat = document.getElementById('categoria').value;

    // Mostra il blocco corrispondente se c'è un valore
    if (cat) {
        const target = document.getElementById('fields-' + cat);
        if (target) target.style.display = 'block';
    }
}

// Esegui la funzione al caricamento della pagina per mostrare i campi corretti in modifica
window.addEventListener('DOMContentLoaded', toggleFields);