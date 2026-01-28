<?php
// ../config/shop_functions.php

/**
 * Restituisce il path dell'immagine placeholder in base alla categoria
 */
function getImagePlaceholder($categoria) {
    // Nota: Aggiusta il path in base a dove includi il file.
    // Se lo includi in pages/product.php, il path relativo è corretto così.
    $basePath = '../../assets/images/';
    switch ($categoria) {
        case 'bevande': return $basePath . 'placeholder_tea.svg';
        case 'merchandising': return $basePath . 'placeholder_merch2.webp';
        case 'servizi': return $basePath . 'placeholder_service.svg';
        case 'bundle': return $basePath . 'placeholder_bundle.svg';
        default: return $basePath . 'placeholder_generic.jpg';
    }
}

/**
 * Esegue la query per ottenere i prodotti (uno singolo o lista)
 */
function getProductQuery($conn, $id = null) {
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

    if ($id !== null) {
        $sql .= " AND P.id = :id";
        $stmt = $conn->prepare($sql);
        // Usiamo PARAM_STR perché nel DB l'ID è un CHAR(36) UUID
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    } else {
        $stmt = $conn->prepare($sql);
    }

    return $stmt;
}

function getBundleItems($conn, $bundleId) {
    $sql = "SELECT P.id, P.nome 
            FROM Bundle B
            JOIN Prodotti P ON B.contenuto = P.id
            WHERE B.id_bundle = :bundleId";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':bundleId', $bundleId, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function loadErrorPage($code) {
    http_response_code($code);

// ADATTA QUESTO PERCORSO:
// Supponiamo che le pagine siano tipo: pages/404.php, pages/500.php
    $path = __DIR__ . "/../pages/$code.html";

// Se sono .html cambia l'estensione qui sotto:
    if (!file_exists($path)) {
        $path = __DIR__ . "/../pages/$code.php";
    }

    if (file_exists($path)) {
        require $path;
    } else {
// Fallback estremo se manca anche il file di errore
        echo "<h1>Errore $code</h1><p>Si è verificato un errore e la pagina personalizzata non è stata trovata.</p>";
    }
    exit; // Importante: ferma l'esecuzione dello script!
}