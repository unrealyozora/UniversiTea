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
    $img_alt = htmlspecialchars($product['img_alt'], ENT_QUOTES, 'UTF-8');

    $img_src=checkImage($product);

    $replacements = [
        '{{ID}}' => $id,
        '{{NOME}}' => $nome,
        '{{DESCRIZIONE}}' => $descrizione,
        '{{PREZZO}}' => $prezzo,
        '{{CATEGORIA}}' => $categoria,
        '{{IMG_PATH}}' => $img_src,
        '{{IMG_ALT}}' => $img_alt,
    ];
    return str_replace(array_keys($replacements), array_values($replacements), $templateHtml);
}


$searchValue = $_GET['search'] ?? '';

$categoryFilter = $_GET['category'] ?? 'tutti';
$maxPrice = $_GET['max-price'] ?? 100;
if (!is_numeric($maxPrice) || $maxPrice < 0) {
    $maxPrice = 100;
}

$onlyAvailable = isset($_GET['availability']) && $_GET['availability'] === 'on';

$checkedTutti = ($categoryFilter === 'tutti') ? 'checked' : '';
$checkedBevande = ($categoryFilter === 'bevande') ? 'checked' : '';
$checkedMerch = ($categoryFilter === 'merchandising') ? 'checked' : '';
$checkedServizi = ($categoryFilter === 'servizi') ? 'checked' : '';
$checkedBundle = ($categoryFilter === 'bundle') ? 'checked' : '';

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
                P.img_src,
                P.img_alt,
                -- Determiniamo la categoria con subquery per coerenza
                CASE 
                    WHEN EXISTS (SELECT 1 FROM Bevande WHERE id = P.id) THEN 'bevande'
                    WHEN EXISTS (SELECT 1 FROM March_Bevande WHERE id = P.id) THEN 'merchandising'
                    WHEN EXISTS (SELECT 1 FROM Servizi WHERE id = P.id) THEN 'servizi'
                    WHEN EXISTS (SELECT 1 FROM Bundle WHERE id_bundle = P.id) THEN 'bundle'
                    ELSE 'altro'
                END as categoria
            FROM Prodotti P
            WHERE 1=1";

    $params = [];

    if ($categoryFilter && $categoryFilter !== 'tutti') {
        if ($categoryFilter === 'bevande') {
            $sql .= " AND EXISTS (SELECT 1 FROM Bevande B WHERE B.id = P.id)";
        } elseif ($categoryFilter === 'merchandising') {
            $sql .= " AND EXISTS (SELECT 1 FROM March_Bevande M WHERE M.id = P.id)";
        } elseif ($categoryFilter === 'servizi') {
            $sql .= " AND EXISTS (SELECT 1 FROM Servizi S WHERE S.id = P.id)";
        } elseif ($categoryFilter === 'bundle') {
            // Qui risolviamo il problema del "moltiplicare i bundle"
            $sql .= " AND EXISTS (SELECT 1 FROM Bundle BU WHERE BU.id_bundle = P.id)";
        }
    }

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

    $sql .= " ORDER BY P.nome ASC";
    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cardTemplate = __DIR__ . '/templates/productCard_template.html';
    $cardTemplate = file_exists($cardTemplate) ? file_get_contents($cardTemplate) : '<li class="no-result"> Template Prodotto Mancante</li>';


    $listaHtml = '';
    foreach ($prodotti as $prodotto) {
        $listaHtml .= renderProductCard($prodotto, $cardTemplate);
    }

    $statusMsg = count($prodotti) > 0
        ? "Visualizzati " . count($prodotti) . " prodotti."
        : "Nessun prodotto trovato con questi filtri.";

} catch (PDOException $e) {
    $statusMsg = "Errore nel caricamento dei prodotti. Riprova più tardi.";
    $listaHtml = '<div class="error-msg"><p>Il magazziniere ha rabaltato qualcosa nel retrobottega.</p> <p>Non ti preoccupare, ricarica la pagina, riprova più tardi o <a href="./about.html">contattaci</a></p></div>';
    error_log("Errore database shop.php: " . $e->getMessage());

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


$htmlContent = file_get_contents('./shop.html');

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

$finalHtml = str_replace(
    array_keys($replacements),
    array_values($replacements),
    $htmlContent
);

echo $finalHtml;