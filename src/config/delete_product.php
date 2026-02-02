<?php
session_start();
require_once 'database/database_conn.php';

// 1. Controllo Sicurezza: Solo Venditore
if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_utente'] !== 'Venditore') {
    header('Location: ../pages/login.php');
    exit();
}
$id_venditore = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = "ID prodotto mancante.";
        header('Location: ../pages/administrator.php');
        exit();
    }

    $conn = null;

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Inizia Transazione
        $conn->beginTransaction();

        $check = $conn->prepare("SELECT 1 FROM Vendita WHERE venditore = :v AND prodotto = :p");
        $check->execute([':v' => $id_venditore, ':p' => $id]);

        if (!$check->fetch()) {
            throw new Exception("Operazione negata: non sei il proprietario di questo prodotto.");
        }

        // ORDINE DI CANCELLAZIONE FONDAMENTALE (Dall'esterno verso l'interno)

        // A. Pulisci interazioni Utente
        // Tabelle: Carrello, Preferiti, Vendita, Consumo
        $tablesInteractions = ['Carrello', 'Preferiti', 'Vendita'];
        foreach ($tablesInteractions as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE prodotto = :id");
            $stmt->execute([':id' => $id]);
        }

        // B. Gestione Dipendenze MERCHANDISING -> BEVANDE
        // Se sto cancellando una Bevanda, devo gestire il Merch collegato
        // Poiché id_bevanda è NOT NULL in March_Bevande, devo cancellare anche il merch collegato
        // o il DB darà errore. Qui optiamo per cancellazione a cascata.
        $stmtMerchCheck = $conn->prepare("DELETE FROM March_Bevande WHERE id_bevanda = :id");
        $stmtMerchCheck->execute([':id' => $id]);

        // C. Gestione BUNDLE
        // 1. Se il prodotto è un Bundle (cancello la "ricetta")
        $stmtB1 = $conn->prepare("DELETE FROM Bundle WHERE id_bundle = :id");
        $stmtB1->execute([':id' => $id]);

        // 2. Se il prodotto è CONTENUTO in un Bundle
        $stmtB2 = $conn->prepare("DELETE FROM Bundle WHERE contenuto = :id");
        $stmtB2->execute([':id' => $id]);

        // D. Cancella dalle Sottotabelle (Specializzazioni)
        $subTables = ['Bevande', 'March_Bevande', 'Servizi'];
        foreach ($subTables as $table) {
            $stmt = $conn->prepare("DELETE FROM $table WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }

        // E. Infine, cancella il Padre
        $stmtFinal = $conn->prepare("DELETE FROM Prodotti WHERE id = :id");
        $stmtFinal->execute([':id' => $id]);

        // Conferma modifiche
        $conn->commit();

        $_SESSION['msg_type'] = 'success';
        $_SESSION['msg_content'] = "Prodotto eliminato definitivamente.";

    } catch (Exception $e) {
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = "Errore durante l'eliminazione: " . $e->getMessage();
    }

    header('Location: ../pages/administrator.php');
    exit();
}