<?php
session_start();
require_once '../config/database/database_conn.php';

// Controllo Admin
if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_utente'] !== 'Venditore') {
    header('Location: login.html');
    exit();
}

$id = $_GET['id'] ?? null;
$isEditing = !empty($id);

// --- Dati di Default (Vuoti) ---
$data = [
    'id' => '', 'nome' => '', 'descrizione' => '', 'prezzo' => '', 'disponibilita' => '',
    'categoria' => '',
    // Campi specifici
    'temp_consigliata' => '', 'tipologia_bevanda' => '',
    'materiale' => '', 'tipologia_march' => '',
    'tipologia_servizi' => '', 'livello_urgenza' => ''
];

$pageTitle = "Nuovo Prodotto";
$btnText = "Crea Prodotto";

// --- LOGICA DI CARICAMENTO (MODIFICA) ---
if ($isEditing) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        // 1. Recupera dati base
        $stmt = $conn->prepare("SELECT * FROM Prodotti WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $data = array_merge($data, $product); // Unisci ai default
            $pageTitle = "Modifica: " . htmlspecialchars($product['nome']);
            $btnText = "Aggiorna Prodotto";

            // 2. Cerca nelle sottotabelle per trovare la categoria e i dettagli

            // Check Bevande
            $stmtBev = $conn->prepare("SELECT * FROM Bevande WHERE id = :id");
            $stmtBev->execute([':id' => $id]);
            if ($row = $stmtBev->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'bevande';
                $data['temp_consigliata'] = $row['temp_consigliata'];
                $data['tipologia_bevanda'] = $row['tipologia_bevanda'];
            }

            // Check Merch
            $stmtMerch = $conn->prepare("SELECT * FROM March_Bevande WHERE id = :id");
            $stmtMerch->execute([':id' => $id]);
            if ($row = $stmtMerch->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'merchandising'; // Deve combaciare col value della select HTML
                $data['materiale'] = $row['Materiale']; // Occhio alla maiuscola nel DB
                $data['tipologia_march'] = $row['tipologia_march'];
            }

            // Check Servizi
            $stmtServ = $conn->prepare("SELECT * FROM Servizi WHERE id = :id");
            $stmtServ->execute([':id' => $id]);
            if ($row = $stmtServ->fetch(PDO::FETCH_ASSOC)) {
                $data['categoria'] = 'servizi';
                $data['tipologia_servizi'] = $row['tipologia_servizi'];
                $data['livello_urgenza'] = $row['livello_urgenza'];
            }
        }
    } catch (PDOException $e) { die("Errore DB: " . $e->getMessage()); }
}

// --- RENDER DEL TEMPLATE ---
$template = file_get_contents(__DIR__ . '/templates/product_form_template.html');

$replacements = [
    '{{TITOLO_PAGINA}}'   => $pageTitle,
    '{{TESTO_BOTTONE}}'   => $btnText,
    '{{VAL_ID}}'          => $data['id'],
    '{{VAL_NOME}}'        => htmlspecialchars($data['nome']),
    '{{VAL_DESCRIZIONE}}' => htmlspecialchars($data['descrizione']),
    '{{VAL_PREZZO}}'      => $data['prezzo'],
    '{{VAL_DISPONIBILITA}}'=> $data['disponibilita'],

    // Logica Selected
    '{{SELECTED_BEVANDE}}' => ($data['categoria'] === 'bevande') ? 'selected' : '',
    '{{SELECTED_MERCH}}'   => ($data['categoria'] === 'merchandising') ? 'selected' : '',
    '{{SELECTED_SERVIZI}}' => ($data['categoria'] === 'servizi') ? 'selected' : '',

    // Valori Specifici
    '{{VAL_TEMP}}'        => htmlspecialchars($data['temp_consigliata']),
    '{{VAL_TIPO_BEV}}'    => htmlspecialchars($data['tipologia_bevanda']),
    '{{VAL_MATERIALE}}'   => htmlspecialchars($data['materiale']),
    '{{VAL_TIPO_MERCH}}'  => htmlspecialchars($data['tipologia_march']),
    '{{VAL_TIPO_SERV}}'   => htmlspecialchars($data['tipologia_servizi']),
    '{{VAL_URGENZA}}'     => htmlspecialchars($data['livello_urgenza']),
];

echo str_replace(array_keys($replacements), array_values($replacements), $template);
