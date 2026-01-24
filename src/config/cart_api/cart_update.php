<?php
// cart_update.php
session_start();
header('Content-Type: application/json');
require_once '../database/database_conn.php'; // Verifica percorso

if (!isset($_SESSION['email'])) { // Verifica login come nel tuo sistema
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id']) || !isset($data['quantity'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit();
}

try {
    $db = (new Database())->getConnection();
    $email = $_SESSION['email'];
    $pid = $data['product_id'];
    $qty = (int)$data['quantity'];

    if ($qty > 0) {
        $stmt = $db->prepare("UPDATE Carrello SET quantita = :qty WHERE consumatore = :email AND prodotto = :pid");
        $stmt->execute([':qty' => $qty, ':email' => $email, ':pid' => $pid]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Quantità non valida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}