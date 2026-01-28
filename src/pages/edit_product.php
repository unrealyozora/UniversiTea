<?php
session_start();
require_once '../config/database/database_conn.php';

// Controllo Admin
if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_utente'] !== 'Venditore') {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? null;
$isEditing = !empty($id);

// --- Dati di Default ---
$data = [
    'id' => '', 'nome' => '', 'descrizione' => '', 'prezzo' => '', 'disponibilita' => '',
    'categoria' => '',
    // Campi specifici
    'temp_consigliata' => '', 'tipologia_bevanda' => '', 'scoop' => '', // Aggiunto scoop
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

    // --- 1. PREPARAZIONE DATI PER I MENU (Select e Checkbox) ---

    // A. Lista Bevande (per Merchandising)
    // Selezioniamo ID e Nome di prodotti che sono anche nella tabella Bevande
    $sqlBev = "SELECT p.id, p.nome FROM Prodotti p JOIN Bevande b ON p.id = b.id ORDER BY p.nome";
    $stmtBev = $conn->query($sqlBev);
    $allBevande = $stmtBev->fetchAll(PDO::FETCH_ASSOC);

    // B. Lista Tutti i Prodotti (per Bundle)
    $sqlProd = "SELECT id, nome FROM Prodotti ORDER BY nome";
    $stmtProd = $conn->query($sqlProd);
    $allProducts = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

    // --- 2. LOGICA DI CARICAMENTO (SE IN MODIFICA) ---
    if ($isEditing) {
        $stmt = $conn->prepare("SELECT * FROM Prodotti WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $data = array_merge($data, $product);
            $pageTitle = "Modifica: " . htmlspecialchars($product['nome']);
            $btnText = "Aggiorna Prodotto";

            // Check Categorie
            // Bevande
            $stmtBev = $conn->prepare("SELECT * FROM Bevande WHERE id = :id");
            $stmtBev->execute([':id' => $id]);
            if ($row = $stmtBev->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'bevande';
                $data['temp_consigliata'] = $row['temp_consigliata'];
                $data['tipologia_bevanda'] = $row['tipologia_bevanda'];
                $data['scoop'] = $row['scoop'];
            }

            // Merchandising
            $stmtMerch = $conn->prepare("SELECT * FROM March_Bevande WHERE id = :id");
            $stmtMerch->execute([':id' => $id]);
            if ($row = $stmtMerch->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'merchandising';
                $data['materiale'] = $row['Materiale'];
                $data['tipologia_march'] = $row['tipologia_march'];
                $data['id_bevanda_assoc'] = $row['id_bevanda']; // ID per preselezionare la option
            }

            // Servizi
            $stmtServ = $conn->prepare("SELECT * FROM Servizi WHERE id = :id");
            $stmtServ->execute([':id' => $id]);
            if ($row = $stmtServ->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'servizi';
                $data['tipologia_servizi'] = $row['tipologia_servizi'];
                $data['livello_urgenza'] = $row['livello_urgenza'];
            }

            // Bundle
            // Nota: Un bundle ha più righe nella tabella Bundle (una per ogni prodotto contenuto)
            // Prendiamo lo sconto dalla prima riga trovata
            $stmtBundle = $conn->prepare("SELECT * FROM Bundle WHERE id_bundle = :id");
            $stmtBundle->execute([':id' => $id]);
            $bundleRows = $stmtBundle->fetchAll(PDO::FETCH_ASSOC);
            if (count($bundleRows) > 0) {
                $data['categoria'] = 'bundle';
                $riga = $bundleRows[0];
                $sconto = $riga['precent_sconto'] ?? $riga['percent_sconto'] ?? 0;

                $data['percent_sconto'] = $sconto;

                // Raccogliamo gli ID dei prodotti inclusi in questo bundle
                foreach($bundleRows as $bRow) {
                    $productsInBundle[] = $bRow['contenuto'];
                }
            }
        }
    }

    // --- 3. GENERAZIONE HTML DINAMICO ---

    // Genera Options Bevande
    $optionsBevandeHtml = '';
    foreach ($allBevande as $bev) {
        $selected = ($bev['id'] == $data['id_bevanda_assoc']) ? 'selected' : '';
        $optionsBevandeHtml .= "<option value=\"{$bev['id']}\" $selected>" . htmlspecialchars($bev['nome']) . "</option>";
    }

    // Genera Checkbox Prodotti per Bundle
    $checkboxProdottiHtml = '';
    foreach ($allProducts as $prod) {
        // Non mostrare il prodotto stesso se siamo in modifica (evita ricorsione)
        if ($isEditing && $prod['id'] == $id) continue;

        $checked = (in_array($prod['id'], $productsInBundle)) ? 'checked' : '';
        $checkboxProdottiHtml .= "
            <div style='margin-bottom: 5px;'>
                <input type='checkbox' id='prod_{$prod['id']}' name='prodotti_bundle[]' value='{$prod['id']}' $checked>
                <label for='prod_{$prod['id']}' style='display:inline; font-weight:normal;'>" . htmlspecialchars($prod['nome']) . "</label>
            </div>";
    }

    $tipiServiziDB = [
        'Fornitura appunti',
        'Decifrazione scrittura del professore',
        'Assistenza a progetto',
        'Ripetizioni',
        'Preparazione all"esame', // Nota: nel DB c'è il doppio apice
        'Sbobine di lezione',
        'Prestito libri di corso'
    ];

    $livelliUrgenzaDB = ['Molto basso', 'Basso', 'Medio', 'Alto', 'Molto alto'];

    // Genera HTML per Select Tipologia Servizi
    $optionsServizi = '<option value="">-- Seleziona Tipo --</option>';
    foreach ($tipiServiziDB as $tipo) {
        $sel = ($data['tipologia_servizi'] === $tipo) ? 'selected' : '';
        // htmlspecialchars è fondamentale qui per gestire le virgolette nel nome
        $optionsServizi .= "<option value=\"" . htmlspecialchars($tipo) . "\" $sel>" . htmlspecialchars($tipo) . "</option>";
    }

    // Genera HTML per Select Urgenza
    $optionsUrgenza = '<option value="">-- Seleziona Urgenza --</option>';
    foreach ($livelliUrgenzaDB as $liv) {
        $sel = ($data['livello_urgenza'] === $liv) ? 'selected' : '';
        $optionsUrgenza .= "<option value=\"$liv\" $sel>$liv</option>";
    }

} catch (PDOException $e) { die("Errore DB: " . $e->getMessage()); }

// --- RENDER DEL TEMPLATE ---
$template = file_get_contents(__DIR__ . '/templates/product_form_template.html');

$replacements = [
    '{{TITOLO_PAGINA}}'   => $pageTitle,
    '{{TESTO_BOTTONE}}'   => $btnText,
    '{{VAL_ID}}'          => $data['id'],
    '{{VAL_NOME}}'        => htmlspecialchars($data['nome']),
    '{{VAL_DESCRIZIONE}}' => htmlspecialchars($data['descrizione']),
    '{{VAL_PREZZO}}'      => $data['prezzo'],
    '{{VAL_DISPONIBILITA}}'=> $data['disponibilita'],

    // Selected Logic
    '{{SELECTED_BEVANDE}}' => ($data['categoria'] === 'bevande') ? 'selected' : '',
    '{{SELECTED_MERCH}}'   => ($data['categoria'] === 'merchandising') ? 'selected' : '',
    '{{SELECTED_SERVIZI}}' => ($data['categoria'] === 'servizi') ? 'selected' : '',
    '{{SELECTED_BUNDLE}}'  => ($data['categoria'] === 'bundle') ? 'selected' : '',

    '{{SELECTED_ACCESSORI}}'     => ($data['tipologia_march'] === 'Accessori') ? 'selected' : '',
    '{{SELECTED_ABBIGLIAMENTO}}' => ($data['tipologia_march'] === 'Abbigliamento') ? 'selected' : '',
    '{{SELECTED_CASA}}'          => ($data['tipologia_march'] === 'Prodotti per la casa') ? 'selected' : '',

    '{{SELECTED_TÈ}}'         => ($data['tipologia_bevanda'] === 'Tè') ? 'selected' : '',
    '{{SELECTED_CIOCCOLATO}}' => ($data['tipologia_bevanda'] === 'Cioccolato') ? 'selected' : '',
    '{{SELECTED_INFUSO}}'     => ($data['tipologia_bevanda'] === 'Infuso') ? 'selected' : '',


    // Placeholders Campi
    '{{VAL_TEMP}}'        => htmlspecialchars($data['temp_consigliata']),
    '{{VAL_SCOOP}}'       => htmlspecialchars($data['scoop']),
    '{{VAL_MATERIALE}}'   => htmlspecialchars($data['materiale']),
    '{{VAL_TIPO_MERCH}}'  => htmlspecialchars($data['tipologia_march']),
    '{{VAL_TIPO_SERV}}'   => htmlspecialchars($data['tipologia_servizi']),
    '{{VAL_URGENZA}}'     => htmlspecialchars($data['livello_urgenza']),
    '{{VAL_SCONTO}}'      => htmlspecialchars($data['percent_sconto']),

    // HTML Generato
    '{{OPTIONS_BEVANDE}}' => $optionsBevandeHtml,
    '{{CHECKBOX_PRODOTTI}}'=> $checkboxProdottiHtml,
    '{{OPTIONS_TIPO_SERVIZI}}' => $optionsServizi,
    '{{OPTIONS_URGENZA}}' => $optionsUrgenza
];

echo str_replace(array_keys($replacements), array_values($replacements), $template);