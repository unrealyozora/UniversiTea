<?php
session_start();
require_once '../config/database/database_conn.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_utente'] !== 'Venditore') {
    header('Location: login.php');
    exit();
}

$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['form_data'] ?? [];
unset($_SESSION['errors'], $_SESSION['form_data']);

$id = $_GET['id'] ?? null;
$isEditing = !empty($id);


$data = [
    'id' => '', 'nome' => '', 'descrizione' => '', 'prezzo' => '', 'disponibilita' => '',
    'categoria' => '', 'img_alt' => '',

    'temp_consigliata' => '', 'tipologia_bevanda' => '', 'scoop' => '',
    'materiale' => '', 'tipologia_march' => '', 'id_bevanda_assoc' => '',
    'tipologia_servizi' => '', 'livello_urgenza' => '',
    'percent_sconto' => ''
];

$pageTitle = "Nuovo Prodotto";
$btnText = "Crea Prodotto";
$productsInBundle = []; // Array per tracciare i prodotti nel bundle

try {
    $db = new Database();
    $conn = $db->getConnection();

    $sqlBev = "SELECT p.id, p.nome FROM Prodotti p JOIN Bevande b ON p.id = b.id ORDER BY p.nome";
    $stmtBev = $conn->query($sqlBev);
    $allBevande = $stmtBev->fetchAll(PDO::FETCH_ASSOC);

    $sqlProdVenditore = "SELECT p.id, p.nome, p.prezzo, p.disponibilita, 
            CASE 
                WHEN p.id IN (SELECT id FROM Bevande) THEN 'Bevanda'
                WHEN p.id IN (SELECT id FROM March_Bevande) THEN 'Merch'
                WHEN p.id IN (SELECT id FROM Servizi) THEN 'Servizio'
                WHEN p.id IN (SELECT id_bundle FROM Bundle) THEN 'Bundle'
                ELSE 'Altro'
            END as categoria
            FROM Prodotti p
            INNER JOIN Vendita v ON p.id = v.prodotto
            WHERE v.venditore = :email
            ORDER BY p.id DESC";

    $stmt = $conn->prepare($sqlProdVenditore);
    $email_venditore = $_SESSION['email'];
    $stmt->execute([':email' => $email_venditore]);
    $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($oldInput)) {
        // Se torniamo da un errore, usiamo i dati che l'utente aveva appena scritto
        $data = array_merge($data, $oldInput);
        if (isset($oldInput['prodotti_bundle']) && is_array($oldInput['prodotti_bundle'])) {
            $productsInBundle = $oldInput['prodotti_bundle'];
        }
    }
    elseif ($isEditing) {
        $stmt = $conn->prepare("SELECT * FROM Prodotti WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $data = array_merge($data, $product);
            $pageTitle = "Modifica: " . htmlspecialchars($product['nome']);
            $btnText = "Aggiorna Prodotto";

            $stmtBev = $conn->prepare("SELECT * FROM Bevande WHERE id = :id");
            $stmtBev->execute([':id' => $id]);
            if ($row = $stmtBev->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'bevande';
                $data['temp_consigliata'] = $row['temp_consigliata'];
                $data['tipologia_bevanda'] = $row['tipologia_bevanda'];
                $data['scoop'] = $row['scoop'];
            }

            $stmtMerch = $conn->prepare("SELECT * FROM March_Bevande WHERE id = :id");
            $stmtMerch->execute([':id' => $id]);
            if ($row = $stmtMerch->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'merchandising';
                $data['materiale'] = $row['Materiale'];
                $data['tipologia_march'] = $row['tipologia_march'];
                $data['id_bevanda_assoc'] = $row['id_bevanda']; // ID per preselezionare la option
            }

            $stmtServ = $conn->prepare("SELECT * FROM Servizi WHERE id = :id");
            $stmtServ->execute([':id' => $id]);
            if ($row = $stmtServ->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'servizi';
                $data['tipologia_servizi'] = $row['tipologia_servizi'];
                $data['livello_urgenza'] = $row['livello_urgenza'];
            }

            $stmtBundle = $conn->prepare("SELECT * FROM Bundle WHERE id_bundle = :id");
            $stmtBundle->execute([':id' => $id]);
            $bundleRows = $stmtBundle->fetchAll(PDO::FETCH_ASSOC);
            if (count($bundleRows) > 0) {
                $data['categoria'] = 'bundle';
                $riga = $bundleRows[0];
                $sconto = $riga['precent_sconto'] ?? $riga['percent_sconto'] ?? 0;

                $data['percent_sconto'] = $sconto;

                foreach($bundleRows as $bRow) {
                    $productsInBundle[] = $bRow['contenuto'];
                }
            }
        }
    }

    $optionsBevandeHtml = '';
    foreach ($allBevande as $bev) {
        $selected = ($bev['id'] == $data['id_bevanda_assoc']) ? 'selected' : '';
        $optionsBevandeHtml .= "<option value=\"{$bev['id']}\" $selected>" . htmlspecialchars($bev['nome']) . "</option>";
    }

    $checkboxProdottiHtml = '';
    foreach ($allProducts as $prod) {
        if ($isEditing && $prod['id'] == $id) continue;

        $checked = (in_array($prod['id'], $productsInBundle)) ? 'checked' : '';
        $checkboxProdottiHtml .= "
            <div class='checkbox-row'>
                <input type='checkbox' id='prod_{$prod['id']}' name='prodotti_bundle[]' value='{$prod['id']}' $checked>
                <label for='prod_{$prod['id']}'>" . htmlspecialchars($prod['nome']) . "
                </label>
            </div>";
    }

    $tipiServiziDB = [
        'Fornitura appunti',
        'Decifrazione scrittura del professore',
        'Assistenza a progetto',
        'Ripetizioni',
        'Preparazione all"esame',
        'Sbobine di lezione',
        'Prestito libri di corso'
    ];

    $livelliUrgenzaDB = ['Molto basso', 'Basso', 'Medio', 'Alto', 'Molto alto'];
    $optionsServizi = '<option value="">-- Seleziona Tipo --</option>';
    foreach ($tipiServiziDB as $tipo) {
        $sel = ($data['tipologia_servizi'] === $tipo) ? 'selected' : '';
        // htmlspecialchars è fondamentale qui per gestire le virgolette nel nome
        $optionsServizi .= "<option value=\"" . htmlspecialchars($tipo) . "\" $sel>" . htmlspecialchars($tipo) . "</option>";
    }

    $optionsUrgenza = '<option value="">-- Seleziona Urgenza --</option>';
    foreach ($livelliUrgenzaDB as $liv) {
        $sel = ($data['livello_urgenza'] === $liv) ? 'selected' : '';
        $optionsUrgenza .= "<option value=\"$liv\" $sel>$liv</option>";
    }

} catch (PDOException $e) { die("Errore DB: " . $e->getMessage()); }

$template = file_get_contents(__DIR__ . '/templates/product_form_template.html');

$replacements = [
    '{{TITOLO_PAGINA}}'   => $pageTitle,
    '{{TESTO_BOTTONE}}'   => $btnText,
    '{{VAL_ID}}'          => $data['id'],
    '{{VAL_NOME}}'        => htmlspecialchars($data['nome']),
    '{{VAL_DESCRIZIONE}}' => htmlspecialchars($data['descrizione']),
    '{{VAL_PREZZO}}'      => $data['prezzo'],
    '{{VAL_DISPONIBILITA}}'=> $data['disponibilita'],
    '{{VAL_DESCRIZ_IMG}}'=>htmlspecialchars($data['img_alt']),

    '{{SELECTED_BEVANDE}}' => ($data['categoria'] === 'bevande') ? 'selected' : '',
    '{{SELECTED_MERCH}}'   => ($data['categoria'] === 'merchandising') ? 'selected' : '',
    '{{SELECTED_SERVIZI}}' => ($data['categoria'] === 'servizi') ? 'selected' : '',
    '{{SELECTED_BUNDLE}}'  => ($data['categoria'] === 'bundle') ? 'selected' : '',
    '{{BEVANDA}}' => ($data['categoria'] === 'bevande') ? 'selected-field' : '',
    '{{MERCHANDISING}}' => ($data['categoria'] === 'merchandising') ? 'selected-field' : '',
    '{{SERVIZI}}' => ($data['categoria'] === 'servizi') ? 'selected-field' : '',
    '{{BUNDLE}}' => ($data['categoria'] === 'bundle') ? 'selected-field' : '',


    '{{SELECTED_ACCESSORI}}'     => ($data['tipologia_march'] === 'Accessori') ? 'selected' : '',
    '{{SELECTED_ABBIGLIAMENTO}}' => ($data['tipologia_march'] === 'Abbigliamento') ? 'selected' : '',
    '{{SELECTED_CASA}}'          => ($data['tipologia_march'] === 'Prodotti per la casa') ? 'selected' : '',

    '{{SELECTED_TÈ}}'         => ($data['tipologia_bevanda'] === 'Tè') ? 'selected' : '',
    '{{SELECTED_CIOCCOLATO}}' => ($data['tipologia_bevanda'] === 'Cioccolato') ? 'selected' : '',
    '{{SELECTED_INFUSO}}'     => ($data['tipologia_bevanda'] === 'Infuso') ? 'selected' : '',


    '{{VAL_TEMP}}'        => htmlspecialchars($data['temp_consigliata']),
    '{{VAL_SCOOP}}'       => htmlspecialchars($data['scoop']),
    '{{VAL_MATERIALE}}'   => htmlspecialchars($data['materiale']),
    '{{VAL_TIPO_MERCH}}'  => htmlspecialchars($data['tipologia_march']),
    '{{VAL_TIPO_SERV}}'   => htmlspecialchars($data['tipologia_servizi']),
    '{{VAL_URGENZA}}'     => htmlspecialchars($data['livello_urgenza']),
    '{{VAL_SCONTO}}'      => htmlspecialchars($data['percent_sconto']),

    '{{OPTIONS_BEVANDE}}' => $optionsBevandeHtml,
    '{{CHECKBOX_PRODOTTI}}'=> $checkboxProdottiHtml,
    '{{OPTIONS_TIPO_SERVIZI}}' => $optionsServizi,
    '{{OPTIONS_URGENZA}}' => $optionsUrgenza
];


$errorPlaceholders = [];
$fieldNames = [
    'nome', 'descrizione', 'prezzo', 'disponibilita', 'categoria',
    'temp_consigliata', 'tipologia_bevanda', 'materiale',
    'tipologia_march', 'id_bevanda', 'tipologia_servizi', 'livello_urgenza',
    'percent_sconto', 'prodotti_bundle'
];

foreach ($fieldNames as $field) {
    if (isset($errors[$field])) {
        $errorPlaceholders['{{ERR_' . strtoupper($field) . '}}'] =
            "<div class='field-error-msg' aria-live='assertive'>" . $errors[$field] . "</div>";
    } else {
        $errorPlaceholders['{{ERR_' . strtoupper($field) . '}}'] = "";
    }
}
$replacements = array_merge($replacements, $errorPlaceholders);

echo str_replace(array_keys($replacements), array_values($replacements), $template);