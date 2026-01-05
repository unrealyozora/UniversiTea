<?php
session_start();
header('Content-type: application/json');

require_once '../database_conn.php';

if (!isset($_SESSION['username']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$quantity = $data['quantity'];


if ($product_id <= 0 || $quantity <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati non validi']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $user_id = $_SESSION['email'];

    $query = "SELECT disponibilita FROM Prodotti WHERE id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Prodotto non trovato.']);
        exit();
    }

    $product = $stmt->fetch();
    if ($product['disponibilita'] < $quantity) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Quantità non disponibile']);
        exit();
    }

    $query = "SELECT prodotto, quantita FROM Carrello WHERE consumatore =:user_id AND prodotto =:product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $cart_item = $stmt->fetch();
        $new_quantity = $cart_item['quantita'] + $quantity;

        if ($new_quantity > $product['disponibilita']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Quantità totale supera la disponibilità']);
            exit();
        }

        $query = "UPDATE Carrello SET quantita = :new_quantity WHERE prodotto = :product_id AND consumatore = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":new_quantity", $new_quantity);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $message = 'Quantità aggiornata nel carrello';
    } else {
        $query = "INSERT INTO Carrello (consumatore, prodotto, quantita) VALUES(:user_id, :product_id, :quantity)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();

        $message = 'Prodotto aggiunto al carrello';
    }

    echo json_encode(['success' => true, 'message' => $message]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
