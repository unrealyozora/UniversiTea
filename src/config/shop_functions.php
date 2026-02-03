<?php
function getImagePlaceholder($categoria)
{
    $basePath = '../../assets/images/';
    switch ($categoria) {
        case 'bevande':
            return $basePath . 'placeholder_tea.svg';
        case 'merchandising':
            return $basePath . 'placeholder_merch2.webp';
        case 'servizi':
            return $basePath . 'placeholder_service.svg';
        case 'bundle':
            return $basePath . 'placeholder_bundle.svg';
        default:
            return $basePath . 'placeholder_generic.jpg';
    }
}

function getBasePath(): string
{
    return '../../assets/images/';
}

function checkImage($product)
{
    $basePath = getBasePath();
    $imageFile = $product['img_src'];
    $fullPath = $basePath . $imageFile;

    if ($product['img_src'] === '' || !file_exists(__DIR__ . '/' . $fullPath)) {
        $img_src = getImagePlaceholder($product['categoria']);
    } else {
        $fullPath = $basePath . $product['img_src'];
        $img_src = htmlspecialchars($fullPath, ENT_QUOTES, 'UTF-8');
    }
    return $img_src;
}

function getProductQuery($conn, $id = null)
{
    $sql = "SELECT 
                P.id, 
                P.nome, 
                P.descrizione, 
                P.img_src,
                P.img_alt,
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

function getBundleItems($conn, $bundleId)
{
    $sql = "SELECT P.id, P.nome 
            FROM Bundle B
            JOIN Prodotti P ON B.contenuto = P.id
            WHERE B.id_bundle = :bundleId";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':bundleId', $bundleId, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}