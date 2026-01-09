<?php
require_once '../config/database_conn.php';
require_once '../config/shop_functions.php';

// 1. Recupero ID e Validazione Immediata
$productId = $_GET['id'] ?? null;

// Se non c'è l'ID nell'URL, è inutile connettersi al DB: errore 404 subito.
if (!$productId) {
    loadErrorPage(404);
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 2. Recupero dati
    $stmt = getProductQuery($conn, $productId);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // CORREZIONE FONDAMENTALE:
    // Se $product è false (nessun risultato trovato), mostriamo 404
    if (!$product) {
        loadErrorPage(404);
    }

    // --- DA QUI IN POI IL CODICE ESEGUE SOLO SE IL PRODOTTO ESISTE ---

    // Formattazione Prezzo
    $prezzoF = number_format($product['prezzo'], 2, ',', '.');

    // Disponibilità
    $isAvailable = $product['disponibilita'] > 0;
    $availClass = $isAvailable ? 'available' : 'out-of-stock';
    $availText = $isAvailable ? 'Disponibile' : 'Non disponibile';
    $btnDisabled = $isAvailable ? '' : 'disabled';
    $btnText = $isAvailable ? 'Aggiungi al carrello' : 'Esaurito';

    // Immagine
    $imgSrc = getImagePlaceholder($product['categoria']);
    $imgAlt = "Dettaglio del prodotto: " . htmlspecialchars($product['nome']);

    // Generazione HTML Specifiche Tecniche
    $specsHtml = '';
    $innerSpecs = '';

    switch ($product['categoria']) {
        case 'bevande':
            if (!empty($product['temp_consigliata'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Temperatura consigliata:</strong> " . htmlspecialchars($product['temp_consigliata']) . "°C</div>";
            }
            if (!empty($product['tipologia_bevanda'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Tipologia bevanda:</strong> " . htmlspecialchars($product['tipologia_bevanda']) . "</div>";
            }
            break;

        case 'merchandising':
            if (!empty($product['Materiale'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Materiale:</strong> " . htmlspecialchars($product['Materiale']) . "</div>";
            }
            break;

        case 'servizi':
            if (!empty($product['livello_urgenza'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Urgenza:</strong> " . htmlspecialchars($product['livello_urgenza']) . "</div>";
            }
            break;
    }

    if ($innerSpecs) {
        $specsHtml = "<div class='product-specs'><h3>Scheda Tecnica</h3>" . $innerSpecs . "</div>";
    }

    // Caricamento Template
    $htmlContent = file_get_contents(__DIR__ . '/product.html');

    // Sostituzione Placeholder
    $replacements = [
        '{{NAME}}' => htmlspecialchars($product['nome']),
        '{{DESCRIPTION}}' => htmlspecialchars($product['descrizione']),
        '{{PRICE}}' => '€' . $prezzoF,
        '{{CATEGORY}}' => htmlspecialchars($product['categoria']),
        '{{IMG_SRC}}' => $imgSrc,
        '{{IMG_ALT}}' => $imgAlt,
        '{{AVAILABILITY_CLASS}}' => $availClass,
        '{{AVAILABILITY_TEXT}}' => $availText,
        '{{BTN_DISABLED}}' => $btnDisabled,
        '{{BTN_TEXT}}' => $btnText,
        '{{SPECS}}' => $specsHtml
    ];

    echo str_replace(
        array_keys($replacements),
        array_values($replacements),
        $htmlContent
    );

} catch (PDOException $e) {
    error_log("Errore product.php: " . $e->getMessage());
    // In caso di errore DB, meglio usare codice 500
    loadErrorPage(500);
}
?>