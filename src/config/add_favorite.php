<?php
session_start();

require_once 'database/database_conn.php';

if (!isset($_SESSION['username']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorizzato.']);
    exit();
}

$product_id = $_POST['product_id'];


try {
    $database = new Database();
    $db = $database->getConnection();

    $user_id = $_SESSION['email'];

    $query = "SELECT disponibilita FROM Prodotti WHERE id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    echo json_encode(['success' => false, 'message' => $product_id]);
    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Prodotto non trovato.']);
        exit();
    }
    if ($stmt->rowCount() > 0) {
        $query = "INSERT INTO Preferiti (consumatore, prodotto) VALUES (:user_id, :product_id)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
    }
    // Successo: Messaggio feedback
    $_SESSION['msg_type'] = 'success';
    $_SESSION['msg_content'] = 'Prodotto aggiunto ai preferiti!';
} catch (Exception $e) {
    error_log("Errore DB Carrello: " . $e->getMessage());
    $_SESSION['msg_type'] = 'error';
    $_SESSION['msg_content'] = 'Errore durante l\'aggiunta ai preferiti.';
}

// Torna allo shop
header('Location: ../pages/shop.php');
exit();

