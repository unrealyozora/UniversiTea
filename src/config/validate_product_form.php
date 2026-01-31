<?php
function validateField($value, $fieldName, &$errors)
{
    if ($value === null || trim($value) === '') {
        $errors[] = "Il campo '$fieldName' è obbligatorio.";
        return false;
    }
    return true;
}


function validateProductData($postData) {
    $errors = [];
    $addError = function($field, $msg) use (&$errors) {
        if (!isset($errors[$field])) {
            $errors[$field] = $msg;
        }
    };

    function validateLength($value, $fieldName, $maxLength, &$errors, $key) {
        if (strlen(trim($value)) > $maxLength) {
            if (!isset($errors[$key])) {
                $errors[$key] = "Il campo '$fieldName' non può superare i $maxLength caratteri.";
            }
            return false;
        }
        return true;
    }

    if (validateField($postData['nome'] ?? '', 'Nome Prodotto', $dummyErrors)) {
        validateLength($postData['nome'], 'Nome Prodotto', 100, $errors, 'nome');
    } elseif (empty(trim($postData['nome'] ?? ''))) {
        $addError('nome', "Il nome del prodotto è obbligatorio.");
    }

    if($postData['img_src']){
        if (!empty(trim($postData['img_alt'] ?? ''))) {
            validateLength($postData['img_alt'], 'Descrizione', 1000, $errors, 'descrizione');
        } else {
            $addError('img_alt', "La descrizione del prodotto è obbligatoria.");
        }
    }

    $prezzo = filter_var($postData['prezzo'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($prezzo === false || $prezzo <= 0 || $prezzo > 100) {
        $addError('prezzo', "Inserisci un prezzo valido tra 0 e 100.");
    }

    $disponibilita = filter_var($postData['disponibilita'] ?? '', FILTER_VALIDATE_INT);
    if ($disponibilita === false || $disponibilita < 0) {
        $addError('disponibilita', "Quantità non valida, deve essere maggiore di 0.");
    }

    $categoria = $postData['categoria'] ?? '';
    if (empty($categoria)) {
        $addError('categoria', "Seleziona una categoria.");
    }

    if ($categoria === 'bevande') {
        $temp_consigliata = filter_var($postData['temp_consigliata'] ?? '', FILTER_VALIDATE_INT);
        if ($temp_consigliata === false || $temp_consigliata < 0) {
            $addError('disponibilita', "Temperatura non valida, deve essere maggiore di 0.");
        }
        if(empty($postData['tipologia_bevanda'])) $addError('tipologia_bevanda', "Tipo mancante.");
        if (!empty($postData['scoop'])) {
            validateLength($postData['scoop'], 'Scoop', 255, $errors, 'scoop');
        }
    }
    elseif ($categoria === 'merchandising') {
        if (empty(trim($postData['materiale'] ?? ''))) {
            $addError('materiale', "Materiale mancante.");
        } else {
            validateLength($postData['materiale'], 'Materiale', 255, $errors, 'materiale');
        }
        if(empty($postData['tipologia_march'])) $addError('tipologia_march', "Tipo merch mancante.");
        if(empty($postData['id_bevanda'])) $addError('id_bevanda', "Associa una bevanda.");
    }
    elseif ($categoria === 'servizi') {
        validateField($postData['tipologia_servizi'] ?? '', 'Tipologia Servizio', $errors);
        validateField($postData['livello_urgenza'] ?? '', 'Livello Urgenza', $errors);
    }
    elseif ($categoria === 'bundle') {
        $sconto = filter_var($postData['percent_sconto'] ?? '', FILTER_VALIDATE_INT);
        if ($sconto === false || $sconto < 0 || $sconto > 100) {
            $errors[] = "Inserisci una percentuale di sconto valida (tra 0 e 100).";
        }

        $prodottiBundle = $postData['prodotti_bundle'] ?? [];
        if (empty($prodottiBundle)) {
            $errors[] = "Seleziona almeno un prodotto per il bundle.";
        }
    }

    return $errors;
}