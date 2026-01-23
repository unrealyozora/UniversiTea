<?php
session_start();

require_once 'database/database_conn.php';

if (!isset($_SESSION['username']) || !$_SESSION['logged_in']) {
    header("Location: ../pages/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    try {
        $database = new Database();
        $db = $database->getConnection();

        $user_id = $_SESSION['email'];

        $query = "SELECT disponibilita FROM Prodotti WHERE id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $_SESSION['msg_type'] = 'error';
            $_SESSION['msg_content'] = 'Prodotto non esistente';
        }

        $query = "SELECT prodotto FROM Preferiti WHERE prodotto= :product_id and consumatore= :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $query = "INSERT INTO Preferiti (consumatore, prodotto) VALUES (:user_id, :product_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            $_SESSION['msg_type'] = 'success';
            $_SESSION['msg_content'] = 'Prodotto aggiunto ai preferiti!';
        } else {
            $_SESSION['msg_type'] = 'error';
            $_SESSION['msg_content'] = 'Prodotto già aggiunto ai preferiti';
        }
    } catch (Exception $e) {
        error_log("Errore DB Carrello: " . $e->getMessage());
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = 'Errore durante l\'aggiunta ai preferiti.';
    }
}

// Torna allo shop
header('Location: ../pages/shop.php');
exit();

