<?php
session_start();
header('Content-Type: application/json');
require_once '../database_conn.php';

if (!isset($_SESSION['username']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $user_id = $_SESSION['email'];

    $query = 'SELECT
                c.consumatore as cart_id,
                c.quantita,
                p.id as product_id,
                p.nome,
                p.prezzo,
                (p.prezzo * c.quantita) as subtotal
                FROM Carrello c
                INNER JOIN Prodotti p ON c.prodotto = p.id
                WHERE c.consumatore = :user_id
                ';
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->execute();

    $cart_items = $stmt->fetchAll();

    $total = 0;
    foreach ($cart_items as $cart_item) {
        $total += $cart_item['subtotal'];
    }
    echo json_encode([
        'success' => true,
        'cart' => $cart_items,
        'total' => $total,
        'item_count' => count($cart_items)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
