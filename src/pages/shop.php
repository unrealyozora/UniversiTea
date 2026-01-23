<?php
require_once '../config/database/database_conn.php';
require_once '../config/shop_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function renderProductCard($product, $templateHtml)
{
    $id = htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8');
    $nome = htmlspecialchars($product['nome'], ENT_QUOTES, 'UTF-8');
    $descrizione = htmlspecialchars($product['descrizione'] ?? '', ENT_QUOTES, 'UTF-8');
    $prezzo = number_format($product['prezzo'], 2, ',', '.');
    $categoria = htmlspecialchars($product['categoria'], ENT_QUOTES, 'UTF-8');
    $imgPath = getImagePlaceholder($categoria);

    $replacements = [
        '{{ID}}'=> $id,
        '{{NOME}}'=> $nome,
        '{{DESCRIZIONE}}'=> $descrizione,
        '{{PREZZO}}'=> $prezzo,
        '{{CATEGORIA}}'=> $categoria,
        '{{IMG_PATH}}'=> $imgPath,
    ];
    return str_replace(array_keys($replacements), array_values($replacements), $templateHtml);
}

// ==================== GESTIONE FILTRI ====================

// Recupera parametri dalla URL
$searchValue = $_GET['search'] ?? '';

$categoryFilter = $_GET['category'] ?? 'tutti';
$maxPrice = $_GET['max-price'] ?? 100;
if (!is_numeric($maxPrice) || $maxPrice < 0) {
    $maxPrice = 100;
}

$onlyAvailable = isset($_GET['availability']) && $_GET['availability'] ==='on';


// Imposta quale radio button deve essere checked
$checkedTutti = ($categoryFilter === 'tutti') ? 'checked' : '';
$checkedBevande = ($categoryFilter === 'bevande') ? 'checked' : '';
$checkedMerch = ($categoryFilter === 'merchandising') ? 'checked' : '';
$checkedServizi = ($categoryFilter === 'servizi') ? 'checked' : '';
$checkedBundle = ($categoryFilter === 'bundle') ? 'checked' : '';


// ==================== QUERY DATABASE ====================

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Query base con LEFT JOIN
    $sql = "SELECT 
                P.id, 
                P.nome, 
                P.descrizione, 
                P.prezzo, 
                P.disponibilita,
                B.temp_consigliata,
                B.tipologia_bevanda,
                B.scoop,
                M.Materiale,
                M.tipologia_march,
                S.tipologia_servizi,
                S.livello_urgenza,
                CASE 
                    WHEN B.id IS NOT NULL THEN 'bevande'
                    WHEN M.id IS NOT NULL THEN 'merchandising'
                    WHEN S.id IS NOT NULL THEN 'servizi'
                    WHEN BU.id_bundle IS NOT NULL THEN 'bundle'
                    ELSE 'altro'
                END as categoria
            FROM Prodotti P
            LEFT JOIN Bevande B ON P.id = B.id
            LEFT JOIN March_Bevande M ON P.id = M.id
            LEFT JOIN Servizi S ON P.id = S.id
            LEFT JOIN Bundle BU ON P.id = BU.id_bundle
            WHERE 1=1";

    $params = [];

    // Applica filtro categoria
    if ($categoryFilter && $categoryFilter !== 'tutti') {
        if ($categoryFilter === 'bevande') {
            $sql .= " AND B.id IS NOT NULL";
        } elseif ($categoryFilter === 'merchandising') {
            $sql .= " AND M.id IS NOT NULL";
        } elseif ($categoryFilter === 'servizi') {
            $sql .= " AND S.id IS NOT NULL";
        } elseif ($categoryFilter === 'bundle') {
            $sql .= " AND BU.id_bundle IS NOT NULL";
        }
    }

    // Applica filtro ricerca
    if (!empty($searchValue)) {
        $sql .= " AND (P.nome LIKE :searchN OR P.descrizione LIKE :searchD)";
        $params[':searchN'] = '%' . $searchValue . '%';
        $params[':searchD'] = '%' . $searchValue . '%';
    }

    if ($maxPrice < 100) { // Solo se diverso dal default
        $sql .= " AND P.prezzo <= :maxPrice";
        $params[':maxPrice'] = (float)$maxPrice;
    }

    if ($onlyAvailable) {
        $sql .= " AND P.disponibilita > 0";
    }

    // Ordinamento deterministico
    $sql .= " ORDER BY P.nome ASC";

    // Esegui query
    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cardTemplate= __DIR__ . '/templates/productCard_template.html';
    $cardTemplate = file_exists($cardTemplate) ? file_get_contents($cardTemplate) : '<li class="no-result"> Template Prodotto Mancante</li>';

    // Genera HTML dei prodotti
    $listaHtml = '';
    foreach ($prodotti as $prodotto) {
        $listaHtml .= renderProductCard($prodotto, $cardTemplate);
    }

    // Se non ci sono prodotti
    if (empty($listaHtml)) {
        $listaHtml = '<li class="no-results">Nessun prodotto trovato con questi filtri.</li>';
    }

    // Messaggio di stato
    $statusMsg = count($prodotti) > 0
        ? "Visualizzati " . count($prodotti) . " prodotti."
        : "Nessun prodotto trovato con questi filtri.";

} catch (PDOException $e) {
    $statusMsg = "Errore nel caricamento dei prodotti. Riprova più tardi.";
    $listaHtml = '<div class="error-msg"><p>Il magazziniere ha rabaltato qualcosa nel retrobottega.</p> <p>Non ti preoccupare, ricarica la pagina, riprova più tardi o <a href="./about.html">contattaci</a></p></div>';
    error_log("Errore database shop.php: " . $e->getMessage());

    // In caso di errore, imposta valori di default per i checked
    $checkedTutti = 'checked';
    $checkedBevande = '';
    $checkedMerch = '';
    $checkedServizi = '';
    $checkedBundle = '';
}

$userFeedback = '';
if (isset($_SESSION['msg_content'])) {
    $msgClass = ($_SESSION['msg_type'] == 'success') ? 'success-msg' : 'error-msg';
    // Inseriamo il messaggio subito prima della lista prodotti o in alto
    $userFeedback = '<div class="' . $msgClass . '" role="alert">' . htmlspecialchars($_SESSION['msg_content']) . '</div>';

    // Pulizia messaggio dopo la visualizzazione
    unset($_SESSION['msg_type']);
    unset($_SESSION['msg_content']);
}

// ==================== CARICA HTML ====================

// Leggi il file HTML
$htmlContent = file_get_contents('./shop.html');

// Sostituisci i placeholder con i dati reali
$replacements = [
    '{{SEARCH_VALUE}}' => htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'),
    '{{CHECKED_TUTTI}}' => $checkedTutti,
    '{{CHECKED_BEVANDE}}' => $checkedBevande,
    '{{CHECKED_MERCH}}' => $checkedMerch,
    '{{CHECKED_SERVIZI}}' => $checkedServizi,
    '{{CHECKED_BUNDLE}}' => $checkedBundle,
    '{{USER_FEEDBACK}}' => $userFeedback,
    '{{STATUS_MSG}}' => htmlspecialchars($statusMsg, ENT_QUOTES, 'UTF-8'),
    '{{LISTA_PRODOTTI}}' => $listaHtml,
    '{{MAX_PRICE}}' => number_format((int)$maxPrice),
    '{{CHECKED_AVAILABILITY}}' => $onlyAvailable ? 'checked' : '',
];

// Applica le sostituzioni
$finalHtml = str_replace(
    array_keys($replacements),
    array_values($replacements),
    $htmlContent
);

echo $finalHtml;