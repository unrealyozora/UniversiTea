<?php
session_start();
require_once '../config/database/database_conn.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_utente'] !== 'Venditore') {
    die("Accesso negato");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = null;
    $id = $_POST['id'] ?? '';
    // Recupero dati comuni
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $prezzo = $_POST['prezzo'];
    $disponibilita = $_POST['disponibilita'];
    $categoria = $_POST['categoria'];

    try {
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) throw new Exception("Impossibile connettersi al database.");

        $conn->beginTransaction();

        // Funzione UUID (Mantenuta dal tuo codice)
        function guidv4($data = null) {
            $data = $data ?? random_bytes(16);
            assert(strlen($data) == 16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        if (!empty($id)) {
            // --- MODIFICA (UPDATE) ---
            $sql = "UPDATE Prodotti SET nome=:n, descrizione=:d, prezzo=:p, disponibilita=:s WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':n'=>$nome, ':d'=>$descrizione, ':p'=>$prezzo, ':s'=>$disponibilita, ':id'=>$id]);

            // Aggiorna Sottotabelle
            if ($categoria === 'bevande') {
                // CORREZIONE: Aggiunto scoop nell'UPDATE
                $sqlSub = "UPDATE Bevande SET temp_consigliata=:t, tipologia_bevanda=:tp, scoop=:sc WHERE id=:id";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([
                    ':t' => $_POST['temp_consigliata'],
                    ':tp' => $_POST['tipologia_bevanda'],
                    ':sc' => $_POST['scoop'], // Campo mancante aggiunto
                    ':id' => $id
                ]);
            } elseif ($categoria === 'merchandising') {
                $sqlSub = "UPDATE March_Bevande SET Materiale=:m, tipologia_march=:tm, id_bevanda=:idb WHERE id=:id";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([
                    ':m' => $_POST['materiale'],
                    ':tm' => $_POST['tipologia_march'],
                    ':idb' => $_POST['id_bevanda'],
                    ':id' => $id
                ]);
            } elseif ($categoria === 'servizi') {
                $sqlSub = "UPDATE Servizi SET tipologia_servizi=:ts, livello_urgenza=:lu WHERE id=:id";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':ts' => $_POST['tipologia_servizi'], ':lu' => $_POST['livello_urgenza'], ':id' => $id]);
            }elseif ($categoria === 'bundle') {
                // Per i bundle, cancelliamo le vecchie associazioni e le ricreiamo
                $conn->prepare("DELETE FROM Bundle WHERE id_bundle = :id")->execute([':id'=>$id]);

                $prodottiSelezionati = $_POST['prodotti_bundle'] ?? [];
                $sconto = $_POST['percent_sconto'] ?? 0;

                $sqlIns = "INSERT INTO Bundle (id_bundle, contenuto, precent_sconto) VALUES (:idb, :cont, :sc)";
                $stmtIns = $conn->prepare($sqlIns);

                foreach ($prodottiSelezionati as $prodId) {
                    $stmtIns->execute([':idb' => $id, ':cont' => $prodId, ':sc' => $sconto]);
                }
            }

            $msg = "Prodotto aggiornato correttamente!";

        } else {
            // --- NUOVO (INSERT) ---
            $newId = guidv4(); // Genera UUID corretto

            $sql = "INSERT INTO Prodotti (id, nome, descrizione, prezzo, disponibilita) VALUES (:id, :n, :d, :p, :s)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id'=>$newId, ':n'=>$nome, ':d'=>$descrizione, ':p'=>$prezzo, ':s'=>$disponibilita]);

            // IMPORTANTE: RIMOSSO $newId = $conn->lastInsertId();
            // Quella riga cancellava l'UUID appena generato mettendo 0.

            if ($categoria === 'bevande') {
                // CORREZIONE: Aggiunto scoop nell'INSERT
                // Se l'utente non lo compila, mettiamo un valore default per evitare l'errore SQL
                $scoopVal = !empty($_POST['scoop']) ? $_POST['scoop'] : 'Standard';

                $sqlSub = "INSERT INTO Bevande (id, temp_consigliata, tipologia_bevanda, scoop) VALUES (:id, :t, :tp, :sc)";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([
                    ':id' => $newId,
                    ':t' => $_POST['temp_consigliata'],
                    ':tp' => $_POST['tipologia_bevanda'],
                    ':sc' => $scoopVal // Campo mancante aggiunto
                ]);
            } elseif ($categoria === 'merchandising') {
                $idBevandaEsistente = $_POST['id_bevanda'];
                if (empty($idBevandaEsistente)) throw new Exception("Devi selezionare una bevanda associata.");

                $sqlSub = "INSERT INTO March_Bevande (id, Materiale, tipologia_march, id_bevanda) VALUES (:id, :m, :tm, :idb)";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([
                    ':id' => $newId,
                    ':m' => $_POST['materiale'],
                    ':tm' => $_POST['tipologia_march'],
                    ':idb' => $idBevandaEsistente
                ]);
            } elseif ($categoria === 'servizi') {
                $sqlSub = "INSERT INTO Servizi (id, tipologia_servizi, livello_urgenza) VALUES (:id, :ts, :lu)";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([':id' => $newId, ':ts' => $_POST['tipologia_servizi'], ':lu' => $_POST['livello_urgenza']]);
            }elseif ($categoria === 'bundle') {
                $prodottiSelezionati = $_POST['prodotti_bundle'] ?? [];
                if (empty($prodottiSelezionati)) throw new Exception("Seleziona almeno un prodotto per il bundle.");

                $sconto = $_POST['percent_sconto'] ?? 0;
                $sqlSub = "INSERT INTO Bundle (id_bundle, contenuto, precent_sconto) VALUES (:idb, :cont, :sc)";
                $stmtSub = $conn->prepare($sqlSub);

                foreach ($prodottiSelezionati as $prodId) {
                    $stmtSub->execute([':idb' => $newId, ':cont' => $prodId, ':sc' => $sconto]);
                }
            }

            $msg = "Nuovo prodotto creato con successo!";
        }

        $conn->commit();
        $_SESSION['msg_type'] = 'success';
        $_SESSION['msg_content'] = $msg;

    } catch (Exception $e) {
        if ($conn && $conn->inTransaction()) $conn->rollBack();
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = "Errore: " . $e->getMessage();
    }

    header('Location: ../pages/administrator.php');
    exit();
}