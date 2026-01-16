<?php
session_start();
require_once 'database_conn.php';

// Controllo Admin
if (!isset($_SESSION['logged_in']) /* || check ruolo admin */) {
    die("Accesso negato");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. INIZIALIZZA LA VARIABILE PRIMA DEL TRY
    $conn = null;

    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $prezzo = $_POST['prezzo'];
    $disponibilita = $_POST['disponibilita'];
    $categoria = $_POST['categoria'];

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Controlla che la connessione sia valida
        if (!$conn) {
            throw new Exception("Impossibile connettersi al database.");
        }

        $conn->beginTransaction();

        if (!empty($id)) {
            // --- MODIFICA (UPDATE) ---
            $sql = "UPDATE Prodotti SET nome=:n, descrizione=:d, prezzo=:p, disponibilita=:s WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':n'=>$nome, ':d'=>$descrizione, ':p'=>$prezzo, ':s'=>$disponibilita, ':id'=>$id]);

            // Aggiorna Sottotabelle (Logica semplificata per brevità)
            if ($categoria === 'bevande') {
                $sqlSub = "UPDATE Bevande SET temp_consigliata=:t, tipologia_bevanda=:tp WHERE id=:id";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':t' => $_POST['temp_consigliata'], ':tp' => $_POST['tipologia_bevanda'], ':id' => $id]);
            } elseif ($categoria === 'merchandising') {
                $sqlSub = "UPDATE March_Bevande SET Materiale=:m, tipologia_march=:tm WHERE id=:id";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':m' => $_POST['materiale'], ':tm' => $_POST['tipologia_march'], ':id' => $id]);
            } elseif ($categoria === 'servizi') {
                $sqlSub = "UPDATE Servizi SET tipologia_servizi=:ts, livello_urgenza=:lu WHERE id=:id";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':ts' => $_POST['tipologia_servizi'], ':lu' => $_POST['livello_urgenza'], ':id' => $id]);
            }

            $msg = "Prodotto aggiornato correttamente!";

        } else {
            // --- NUOVO (INSERT) ---
            $sql = "INSERT INTO Prodotti (nome, descrizione, prezzo, disponibilita) VALUES (:n, :d, :p, :s)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':n'=>$nome, ':d'=>$descrizione, ':p'=>$prezzo, ':s'=>$disponibilita]);

            $newId = $conn->lastInsertId();

            if ($categoria === 'bevande') {
                $sqlSub = "INSERT INTO Bevande (id, temp_consigliata, tipologia_bevanda) VALUES (:id, :t, :tp)";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':id' => $newId, ':t' => $_POST['temp_consigliata'], ':tp' => $_POST['tipologia_bevanda']]);
            } elseif ($categoria === 'merchandising') {
                $sqlSub = "INSERT INTO March_Bevande (id, Materiale, tipologia_march) VALUES (:id, :m, :tm)";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':id' => $newId, ':m' => $_POST['materiale'], ':tm' => $_POST['tipologia_march']]);
            } elseif ($categoria === 'servizi') {
                $sqlSub = "INSERT INTO Servizi (id, tipologia_servizi, livello_urgenza) VALUES (:id, :ts, :lu)";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':id' => $newId, ':ts' => $_POST['tipologia_servizi'], ':lu' => $_POST['livello_urgenza']]);
            }

            $msg = "Nuovo prodotto creato con successo!";
        }

        $conn->commit();
        $_SESSION['msg_type'] = 'success';
        $_SESSION['msg_content'] = $msg;

    } catch (Exception $e) {
        // 2. CONTROLLA SE $conn ESISTE PRIMA DI USARE ROLLBACK
        if ($conn && $conn->inTransaction()) {
            $conn->rollBack();
        }

        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = "Errore: " . $e->getMessage();
    }

    header('Location: ../pages/administrator.php');
    exit();
}