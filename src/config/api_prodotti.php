<?php
// api_prodotti.php
header('Content-Type: application/json; charset=utf-8');

require_once 'database_conn.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Recuperiamo il filtro dalla URL (?cat=bevande)
    $filtroCategoria = $_GET['cat'] ?? null;
    $filtroId = $_GET['id'] ?? null;

    // QUERY DINAMICA
    // Selezioniamo i dati comuni e "calcoliamo" la categoria al volo
    $sql = "SELECT 
                P.id, 
                P.nome, 
                P.descrizione, 
                P.prezzo, 
                P.disponibilita,
                -- Campi specifici per BEVANDE
                B.temp_consigliata,
                B.tipologia_bevanda,
                B.scoop,
                -- Campi specifici per MERCHANDISING
                M.Materiale,
                M.tipologia_march,
                -- Campi specifici per SERVIZI
                S.tipologia_servizi,
                S.livello_urgenza,
                CASE 
                    WHEN B.id IS NOT NULL THEN 'bevande'
                    WHEN M.id IS NOT NULL THEN 'merchandising' -- March_Bevande
                    WHEN S.id IS NOT NULL THEN 'servizi'
                    WHEN BU.id_bundle IS NOT NULL THEN 'bundle' -- Business
                    ELSE 'altro'
                END as categoria
            FROM Prodotti P
            LEFT JOIN Bevande B ON P.id = B.id
            LEFT JOIN March_Bevande M ON P.id = M.id
            LEFT JOIN Servizi S ON P.id = S.id
            LEFT JOIN Bundle BU ON P.id = BU.id_bundle
            WHERE 1=1";

    if($filtroId){
        $sql .= " AND P.id = :id";
    }
    elseif ($filtroCategoria && $filtroCategoria !== 'tutti') {
        if ($filtroCategoria === 'bevande') {
            $sql .= " AND B.id IS NOT NULL";
        } elseif ($filtroCategoria === 'merchandising') {
            $sql .= " AND M.id IS NOT NULL";
        } elseif ($filtroCategoria === 'servizi') {
            $sql .= " AND S.id IS NOT NULL";
        }
        elseif ($filtroCategoria === 'bundle'){
            $sql .= " AND BU.id_bundle IS NOT NULL";
        }
    }

    // Bland's Rule: Ordinamento deterministico (anche se UUID non è sequenziale, è univoco)
    $sql .= " ORDER BY P.nome ASC";

    $stmt = $conn->prepare($sql);

    if($filtroId){
        $stmt->bindParam(":id", $filtroId, PDO::PARAM_STR);
    }

    $stmt->execute();

    $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($prodotti);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Errore nel database: " . $e->getMessage()]);
}