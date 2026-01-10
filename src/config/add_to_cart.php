<?php
session_start();
require_once 'database_conn.php';

// Se l'utente non è loggato, reindirizza al login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../pages/login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $email = $_SESSION['email'];
    $prodId = $_POST['product_id'];

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // 1. Controlla se il prodotto esiste già nel carrello
        $stmt = $conn->prepare("SELECT * FROM Carrello WHERE consumatore = :email AND prodotto = :prod");
        $stmt->execute([':email' => $email, ':prod' => $prodId]);

        if ($stmt->rowCount() > 0) {
            // 2. Se esiste, aggiorna la quantità (nota: 'quantita' SENZA accento)
            $update = $conn->prepare("UPDATE Carrello SET quantita = quantita + 1 WHERE consumatore = :email AND prodotto = :prod");
            $update->execute([':email' => $email, ':prod' => $prodId]);
        } else {
            // 3. Se non esiste, inserisci (nota: 'quantita' SENZA accento)
            $insert = $conn->prepare("INSERT INTO Carrello (consumatore, prodotto, quantita) VALUES (:email, :prod, 1)");
            $insert->execute([':email' => $email, ':prod' => $prodId]);
        }

        // Successo: Messaggio feedback
        $_SESSION['msg_type'] = 'success';
        $_SESSION['msg_content'] = 'Prodotto aggiunto al carrello! Grazie mille';

    } catch (PDOException $e) {
        error_log("Errore DB Carrello: " . $e->getMessage());
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = 'Errore durante l\'aggiunta al carrello.';
    }
}

// Torna allo shop
header('Location: ../pages/shop.php');
exit();