<?php
session_start();
require_once '../config/database/database_conn.php';
require_once './validate_product_form.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_utente'] !== 'Venditore') {
    die("Accesso negato");
}
$id_venditore = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_category') {
    $_SESSION['form_data'] = $_POST;
    $redirectUrl = '../pages/edit_product.php' . (!empty($_POST['id']) ? "?id=" . $_POST['id'] : "");
    header("Location: $redirectUrl");
    exit();
}

//$errors = validateProductData($_POST);
$errors = [];
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;

    header('Location: ../pages/edit_product.php' . (!empty($_POST['id']) ? "?id=" . $_POST['id'] : ""));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $img_target_dir = "../../assets/images/";
    $target_image = $img_target_dir . basename($_FILES["img_src"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_image, PATHINFO_EXTENSION));
    if ($_FILES["img_src"]["size"] > 5000000) {
        $_SESSION['errors']['immagine'] = "Il file è troppo grande! (Dimensioni massime 5 Megabyte)";
        $uploadOk = 0;
    }
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $_SESSION['errors']['immagine'] = "Sono permessi solo file JPG e PNG.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        $unique_filename = uniqid() . '_' . basename($_FILES["img_src"]["name"]);
        $target_image = $img_target_dir . $unique_filename;
        if (move_uploaded_file($_FILES["img_src"]["tmp_name"], $target_image)) {
            $img_src = $unique_filename;
        } else {
            $_SESSION['errors']['immagine'] = "Errore durante il caricamento del file.";
            $uploadOk = 0;
            $img_src = '';
        }
    } else {
        $_SESSION['form_data'] = $_POST;
        header('Location: ../pages/edit_product.php' . (!empty($_POST['id']) ? "?id=" . $_POST['id'] : ""));
        exit();
    }
    $conn = null;
    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $prezzo = $_POST['prezzo'];
    $disponibilita = $_POST['disponibilita'];
    $categoria = $_POST['categoria'];
    $img_alt = $_POST['img_alt'];

    try {
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) throw new Exception("Impossibile connettersi al database.");

        $conn->beginTransaction();

        function guidv4($data = null)
        {
            $data = $data ?? random_bytes(16);
            assert(strlen($data) == 16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        if (!empty($id)) {
            $checkSql = "SELECT 1 FROM Vendita WHERE venditore = :v AND prodotto = :p";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([':v' => $id_venditore, ':p' => $id]);

            if (!$checkStmt->fetch()) {
                throw new Exception("Non hai i permessi per modificare questo prodotto.");
            }

            $sql = "UPDATE Prodotti SET nome=:n, descrizione=:d, prezzo=:p, disponibilita=:s, img_src=:is, img_alt=:ia WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':n' => $nome, ':d' => $descrizione, ':p' => $prezzo, ':s' => $disponibilita, ':is' => $img_src, ':ia' => $img_alt, ':id' => $id]);

            // Aggiorna Sottotabelle
            if ($categoria === 'bevande') {
                $sqlSub = "UPDATE Bevande SET temp_consigliata=:t, tipologia_bevanda=:tp, scoop=:sc WHERE id=:id";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([
                    ':t' => $_POST['temp_consigliata'],
                    ':tp' => $_POST['tipologia_bevanda'],
                    ':sc' => $_POST['scoop'],
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
            } elseif ($categoria === 'bundle') {
                $conn->prepare("DELETE FROM Bundle WHERE id_bundle = :id")->execute([':id' => $id]);

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
            $newId = guidv4();

            $sql = "INSERT INTO Prodotti (id, nome, descrizione, prezzo, disponibilita, img_src, img_alt) VALUES (:id, :n, :d, :p, :s, :is, :ia)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $newId, ':n' => $nome, ':d' => $descrizione, ':p' => $prezzo, ':s' => $disponibilita, ':is' => $img_src, ':ia' => $img_alt,]);

            $sqlVendita = "INSERT INTO Vendita (venditore, prodotto, quantita) VALUES (:v, :p, :q)";
            $stmtVendita = $conn->prepare($sqlVendita);
            $stmtVendita->execute([
                ':v' => $id_venditore,
                ':p' => $newId,
                ':q' => $disponibilita
            ]);

            if ($categoria === 'bevande') {
                $scoopVal = !empty($_POST['scoop']) ? $_POST['scoop'] : 'Standard';

                $sqlSub = "INSERT INTO Bevande (id, temp_consigliata, tipologia_bevanda, scoop) VALUES (:id, :t, :tp, :sc)";
                $stmtSub = $conn->prepare($sqlSub);
                $stmtSub->execute([
                    ':id' => $newId,
                    ':t' => $_POST['temp_consigliata'],
                    ':tp' => $_POST['tipologia_bevanda'],
                    ':sc' => $scoopVal
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
            } elseif ($categoria === 'bundle') {
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