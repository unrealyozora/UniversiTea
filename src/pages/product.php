<?php
session_start();
require_once '../config/database/database_conn.php';
require_once '../config/shop_functions.php';

// 1. Recupero ID e Validazione Immediata
$productId = $_GET['id'] ?? null;

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 2. Recupero dati
    $stmt = getProductQuery($conn, $productId);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);


    // Formattazione Prezzo
    $prezzoF = number_format($product['prezzo'], 2, ',', '.');

    // Disponibilità
    $isAvailable = $product['disponibilita'] > 0;
    $availClass = $isAvailable ? 'available' : 'out-of-stock';
    $availText = $isAvailable ? 'Disponibile' : 'Non disponibile';
    $btnDisabled = $isAvailable ? '' : 'disabled';
    $btnText = $isAvailable ? 'Aggiungi al carrello' : 'Esaurito';

    $imgSrc=checkImage($product);


    $imgAlt = $product['img_alt'];

    // Generazione HTML Specifiche Tecniche
    $specsHtml = '';
    $innerSpecs = '';

    switch ($product['categoria']) {
        case 'bevande':
            if (!empty($product['temp_consigliata'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Temperatura:</strong> " . htmlspecialchars($product['temp_consigliata']) . "°C</div>";
            }
            if (!empty($product['tipologia_bevanda'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Tipo:</strong> " . htmlspecialchars($product['tipologia_bevanda']) . "</div>";
            }
            if (!empty($product['scoop'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Curiosità (Scoop):</strong> " . htmlspecialchars($product['scoop']) . "</div>";
            }
            break;

        case 'merchandising':
            if (!empty($product['tipologia_march'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Categoria Merch:</strong> " . htmlspecialchars($product['tipologia_march']) . "</div>";
            }
            if (!empty($product['Materiale'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Materiale:</strong> " . htmlspecialchars($product['Materiale']) . "</div>";
            }
            break;

        case 'servizi':
            if (!empty($product['tipologia_servizi'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Tipo di servizio:</strong> " . htmlspecialchars($product['tipologia_servizi']) . "</div>";
            }
            if (!empty($product['livello_urgenza'])) {
                $innerSpecs .= "<div class='spec-item'><strong>Urgenza:</strong> " . htmlspecialchars($product['livello_urgenza']) . "</div>";
            }
            break;

        case 'bundle':
            // Per i bundle potresti voler mostrare lo sconto applicato
            $innerSpecs .= "<div class='spec-item'><strong>Percentuale Sconto:</strong> Prodotto incluso in un pacchetto speciale.</div>";
            $items = getBundleItems($conn, $productId);
            if (!empty($items)) {
                $innerSpecs .= "<div class='spec-item'><strong>Contenuto del Bundle:</strong><ul id='bundle-content'>";
                foreach ($items as $item) {
                    $link = "product.php?id=" . urlencode($item['id']);
                    $nome = htmlspecialchars($item['nome']);
                    $innerSpecs .= "<li><a href='$link'>$nome</a></li>";
                }
                $innerSpecs .= "</ul></div>";
            }
            break;
    }

// Correzione accesso variabile disponibilità
    $isAvailable = $product['disponibilità'] > 0;

    if ($innerSpecs) {
        $specsHtml = "<div class='product-specs'><h3>Scheda Tecnica</h3>" . $innerSpecs . "</div>";
    }

    $feedbackHtml = '';
    if (isset($_SESSION['msg_type']) && isset($_SESSION['msg_content'])) {

        $msgClass = ($_SESSION['msg_type'] === 'success') ? 'alert-success' : 'alert-error';
        $msgContent = htmlspecialchars($_SESSION['msg_content']);

        // Creiamo l'HTML
        $feedbackHtml = "<div class='$msgClass'>$msgContent</div>";

        // PULIZIA: Rimuoviamo il messaggio dalla sessione così non appare di nuovo al refresh
        unset($_SESSION['msg_type']);
        unset($_SESSION['msg_content']);
    }

    $user_action = '';
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $user_action = '<li>
                        <a href="dashboard.php" class="nav-profile-link">
                             <img src="../../assets/images/user.png" alt="Il tuo profilo">
                            <span class="mobile-only-text">Profilo</span>
                        </a>
                    </li>';
    } else {
        $user_action = '<li>
                        <a href="register.php" class="btn-join" aria-label="Accedi ora al tuo account">
                            Accedi Ora
                        </a>
                    </li>';
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
        '{{PRODUCT_ID}}'=> $productId,
        '{{FEEDBACK}}'=> $feedbackHtml,
        '{{AVAILABILITY_CLASS}}' => $availClass,
        '{{AVAILABILITY_TEXT}}' => $availText,
        '{{BTN_DISABLED}}' => $btnDisabled,
        '{{BTN_TEXT}}' => $btnText,
        '{{SPECS}}' => $specsHtml,
        '{{USER_ACTION}}' => $user_action,
    ];

    echo str_replace(
        array_keys($replacements),
        array_values($replacements),
        $htmlContent
    );

} catch (PDOException $e) {
    error_log("Errore product.php: " . $e->getMessage());
}