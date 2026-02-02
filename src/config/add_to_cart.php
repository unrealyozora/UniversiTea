<?php
session_start();
require_once './database/database_conn.php';

// Se l'utente non è loggato, reindirizza al login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $email = $_SESSION['email'];
    $prodId = $_POST['product_id'];

    if (isset($_SESSION['tipo_utente']) && $_SESSION['tipo_utente'] === 'Venditore') {
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = 'L\'aggiunta al carrello è un\'azione bloccata per gli account Venditore. Esegui l\'accesso con un account Standard.';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (str_contains($referer, 'shop.php')) {
            header('Location: ../pages/shop.php');
        } else {
            header("Location: ../pages/product.php?id=" . $prodId);
        }
        exit();
    }

    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // 1. Controlla se il prodotto esiste già nel carrello
        $stmt = $conn->prepare("SELECT * FROM Carrello WHERE consumatore = :email AND prodotto = :prod");
        $stmt->execute([':email' => $email, ':prod' => $prodId]);

        if ($stmt->rowCount() > 0) {
            // 2. Se esiste, aggiorna la quantità (nota: 'quantita' SENZA accento)
            $update = $conn->prepare("UPDATE Carrello SET quantita = quantita + :qty WHERE consumatore = :email AND prodotto = :prod");
            $update->execute([':email' => $email, ':prod' => $prodId, ':qty' => $qty]);
        } else {
            // 3. Se non esiste, inserisci (nota: 'quantita' SENZA accento)
            $insert = $conn->prepare("INSERT INTO Carrello (consumatore, prodotto, quantita) VALUES (:email, :prod, :qty)");
            $insert->execute([':email' => $email, ':prod' => $prodId, ':qty' => $qty]);
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


$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (str_contains($referer, 'shop.php')) {
    header('Location: ../pages/shop.php');
} else {
    header("Location: ../pages/product.php?id=" . $prodId);
}
exit();