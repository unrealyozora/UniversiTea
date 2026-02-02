/**
 * Funzione per gestire la visibilità dei campi in base alla categoria
 */
function toggleFields() {
    // 1. Nascondi tutti i container specifici
    document.querySelectorAll('.specific-fields').forEach(el => {
        el.style.display = 'none';
        // Quando nascondi un blocco, è bene pulire eventuali errori pendenti lì dentro
        clearErrorsInContainer(el);
    });

    // 2. Recupera il valore selezionato
    const cat = document.getElementById('categoria').value;

    // 3. Mostra il blocco corrispondente
    if (cat) {
        const target = document.getElementById('fields-' + cat);
        if (target) {
            target.style.display = 'block';
        }
    }
}

/**
 * Pulisce TUTTI gli errori visivi (usato al submit)
 */
function clearErrors() {
    document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    document.querySelectorAll('.field-error-msg').forEach(el => el.remove());
}

/**
 * Pulisce gli errori in un container specifico (usato nel cambio categoria)
 */
function clearErrorsInContainer(container) {
    container.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    container.querySelectorAll('.field-error-msg').forEach(el => el.remove());
}

/**
 * NUOVA FUNZIONE: Rimuove l'errore da un SINGOLO campo (usato mentre scrivi)
 */
function removeFieldError(inputElement) {
    // 1. Rimuovi il bordo rosso dall'elemento stesso
    inputElement.classList.remove('input-error');

    // 2. Cerca e rimuovi il messaggio di errore (il fratello successivo o nel parent)
    // Dato che showError lo inserisce dopo l'input, controlliamo i fratelli
    const parent = inputElement.parentNode;
    const errorMsg = parent.querySelector('.field-error-msg');

    // Rimuoviamo il messaggio solo se è "vicino" a questo input (per evitare di cancellare errori altrui)
    if (errorMsg) {
        // Controllo extra: rimuoviamo solo se il messaggio segue questo input
        // (Semplificazione: nel 99% dei casi basta rimuovere il msg trovato nel parent diretto)
        errorMsg.remove();
    }

    // 3. CASO SPECIALE: Bundle (Checkbox)
    // Se stiamo cliccando un checkbox dentro il box bundle, l'errore potrebbe essere sul contenitore padre (.bundle-scroll-box)
    if (inputElement.type === 'checkbox') {
        const bundleBox = inputElement.closest('.bundle-scroll-box');
        if (bundleBox && bundleBox.classList.contains('input-error')) {
            bundleBox.classList.remove('input-error');
            const boxErrorMsg = bundleBox.parentNode.querySelector('.field-error-msg');
            if (boxErrorMsg) boxErrorMsg.remove();
        }
    }
}

/**
 * Aggiunge l'errore visivo a un campo specifico
 */
function showError(inputElement, message) {
    inputElement.classList.add('input-error');

    const msg = document.createElement('span');
    msg.className = 'field-error-msg';
    msg.innerText = message;

    if (inputElement.nextElementSibling) {
        inputElement.parentNode.insertBefore(msg, inputElement.nextElementSibling);
    } else {
        inputElement.parentNode.appendChild(msg);
    }
}

/**
 * LOGICA PRINCIPALE
 */
document.addEventListener('DOMContentLoaded', () => {
    // Inizializza visibilità campi
    toggleFields(true);

    // 2. Gestione evento cambio:
    const catSelect = document.getElementById('categoria');

    if (catSelect) {
        catSelect.addEventListener('change', () => {
            toggleFields(false);
        });
    }

    const form = document.getElementById('productForm');
    const allInputs = document.querySelectorAll('#productForm input, #productForm select, #productForm textarea');

    allInputs.forEach(element => {
        // Evento 'input': scatta mentre scrivi (per text, number, textarea)
        element.addEventListener('input', function() {
            removeFieldError(this);
        });

        // Evento 'change': scatta quando cambi valore (per select, checkbox, radio)
        element.addEventListener('change', function() {
            removeFieldError(this);
        });
    });

    if (form) {
        form.addEventListener('submit', function (event) {

            clearErrors();
            let isValid = true;
            let firstErrorField = null;

            const validate = (selector, requiredMsg) => {
                const el = document.querySelector(selector);
                if (!el || el.offsetParent === null) return;

                const value = el.value.trim();

                if (value === '' && requiredMsg) {
                    isValid = false;
                    showError(el, requiredMsg);
                    if (!firstErrorField) firstErrorField = el;
                    return;
                }

                if (el.type === 'number' && value !== '') {
                    const min = parseFloat(el.getAttribute('min'));
                    const max = parseFloat(el.getAttribute('max'));
                    const currentVal = parseFloat(value);

                    if (!isNaN(min) && currentVal < min) {
                        isValid = false;
                        showError(el, `Il valore deve essere almeno ${min}.`);
                        if (!firstErrorField) firstErrorField = el;
                        return;
                    }

                    if (!isNaN(max) && currentVal > max) {
                        isValid = false;
                        showError(el, `Il valore non può superare ${max}.`);
                        if (!firstErrorField) firstErrorField = el;
                        return;
                    }
                }

                if (el.hasAttribute('maxlength') && value !== '') {
                    const maxLen = parseInt(el.getAttribute('maxlength'));
                    if (value.length > maxLen) {
                        isValid = false;
                        showError(el, `Massimo ${maxLen} caratteri consentiti.`);
                        if (!firstErrorField) firstErrorField = el;
                        return;
                    }
                }
            };

            validate('#nome', 'Il nome del prodotto è obbligatorio, al massimo 100 caratteri.');
            validate('#descrizione', 'Inserisci una descrizione, al massimo 1000 caratteri..');
            validate('#prezzo', 'Inserisci un prezzo valido, compreso tra 0.01 e 999.');
            validate('#disponibilita', 'Specifica la quantità maggiore uguale a zero.');
            validate('#categoria', 'Seleziona una categoria.');

            // Validazione condizionale
            const categoria = catSelect.value;

            if (categoria === 'bevande') {
                validate('input[name="temp_consigliata"]', 'Inserisci la temperatura maggiore di 0.');
                validate('select[name="tipologia_bevanda"]', 'Specifica il tipo di bevanda.');
                validate('input[name="scoop"]', null);
            }
            else if (categoria === 'merchandising') {
                validate('input[name="materiale"]', 'Indica il materiale.');
                validate('select[name="tipologia_march"]', 'Seleziona il tipo.');
                validate('select[name="id_bevanda"]', 'Associa una bevanda.');
            }
            else if (categoria === 'servizi') {
                validate('select[name="tipologia_servizi"]', 'Scegli il servizio.');
                validate('select[name="livello_urgenza"]', 'Scegli l\'urgenza.');
            }
            else if (categoria === 'bundle') {
                validate('input[name="percent_sconto"]', null);

                // Logica custom per checkbox (rimane invariata)
                const checkboxes = document.querySelectorAll('#fields-bundle input[type="checkbox"]');
                let checkedOne = false;
                checkboxes.forEach(box => { if (box.checked) checkedOne = true; });

                if (!checkedOne && checkboxes.length > 0) {
                    isValid = false;
                    const container = document.querySelector('.bundle-scroll-box');
                    if(container) showError(container, 'Seleziona almeno un prodotto.');
                    if (!firstErrorField) firstErrorField = container;
                }
            }

            if (!isValid) {
                event.preventDefault();
                if (firstErrorField) {
                    firstErrorField.focus();
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

});