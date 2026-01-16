<?php
session_start();
header('Content-Type: application/json');

require_once '../database/database_conn.php';

// Verifica autenticazione
if (!isset($_SESSION['username']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'];

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID prodotto non valido']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $user_id = $_SESSION['email'];

    $query = "DELETE FROM Carrello WHERE consumatore = :user_id AND prodotto = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Prodotto rimosso dal carrello'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Prodotto non trovato nel carrello',
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore del server: ' . $e->getMessage()]);
}