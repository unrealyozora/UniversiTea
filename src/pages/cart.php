<?php
// cart.php

// 1. SETUP E CONNESSIONE
require_once '../config/auth/check_auth.php'; // Adatta il percorso se necessario
require_once '../config/database/database_conn.php'; // Percorso basato sui tuoi file caricati

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica login (adattato alla tua logica di sessione)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$userEmail = $_SESSION['email']; // I tuoi file usano l'email come ID nel DB

// Helper per template (non toccare)
function renderCartItem($item, $templateHtml) {
    $id = htmlspecialchars($item['id']);
    $nome = htmlspecialchars($item['nome']);
    $prezzo = '€' . number_format($item['prezzo'], 2, ',', '.');
    $subtotale = '€' . number_format($item['subtotal'], 2, ',', '.');
    $qty = (int)$item['quantita']; // Nota: 'quantita' senza accento come nel tuo DB

    // Logica stock: se non hai colonna stock in Carrello, la prendiamo da Prodotti
    // Assumo stock 99 se non definito, oppure aggiungi la colonna nella query
    $stock = isset($item['stock']) ? (int)$item['stock'] : 99;

    $replacements = [
        '{{ID}}' => $id,
        '{{NOME}}' => $nome,
        '{{PREZZO_UNITARIO}}' => $prezzo,
        '{{SUBTOTALE}}' => $subtotale,
        '{{QTY}}' => $qty,
        '{{QTY_MINUS}}' => $qty - 1,
        '{{QTY_PLUS}}' => $qty + 1,
        '{{DISABLED_MINUS}}' => ($qty <= 1) ? 'disabled' : '',
        '{{DISABLED_PLUS}}' => ($qty >= $stock) ? 'disabled' : ''
    ];
    return str_replace(array_keys($replacements), array_values($replacements), $templateHtml);
}

// 2. GESTIONE AZIONI POST (FALLBACK PER QUANDO MANCA JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $prodId = $_POST['product_id'] ?? 0;

    try {
        $db = new Database();
        $conn = $db->getConnection();

        if ($action === 'update_quantity') {
            $qty = (int)$_POST['quantity'];
            if ($qty > 0) {
                // Query adattata alla tua tabella 'Carrello'
                $stmt = $conn->prepare("UPDATE Carrello SET quantita = :qty WHERE consumatore = :email AND prodotto = :pid");
                $stmt->execute([':qty' => $qty, ':email' => $userEmail, ':pid' => $prodId]);
                $_SESSION['msg_content'] = "Quantità aggiornata.";
                $_SESSION['msg_type'] = "success";
            }
        } elseif ($action === 'remove') {
            $stmt = $conn->prepare("DELETE FROM Carrello WHERE consumatore = :email AND prodotto = :pid");
            $stmt->execute([':email' => $userEmail, ':pid' => $prodId]);
            $_SESSION['msg_content'] = "Prodotto rimosso.";
            $_SESSION['msg_type'] = "success";
        } elseif ($action === 'clear') {
            $stmt = $conn->prepare("DELETE FROM Carrello WHERE consumatore = :email");
            $stmt->execute([':email' => $userEmail]);
            $_SESSION['msg_content'] = "Carrello svuotato.";
            $_SESSION['msg_type'] = "success";
        }

    } catch (PDOException $e) {
        $_SESSION['msg_content'] = "Errore database."; // Messaggio generico per utente
        $_SESSION['msg_type'] = "error";
        error_log("Cart Error: " . $e->getMessage());
    }

    // Pattern PRG: Ricarica la pagina pulita
    header("Location: cart.php");
    exit;
}

// 3. RECUPERO DATI E RENDERING (GET)
$cartItemsHtml = '';
$totalAmount = 0;
$totalItems = 0;

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Query corretta basata sul tuo file cart_get.php e add_to_cart.php
    // Recuperiamo anche 'stock' dalla tabella Prodotti se esiste, altrimenti toglilo
    $query = "
        SELECT 
            p.id, 
            p.nome, 
            p.prezzo, 
            c.quantita, 
            (p.prezzo * c.quantita) as subtotal
            -- , p.stock  <-- Decommenta se hai la colonna stock in Prodotti
        FROM Carrello c 
        JOIN Prodotti p ON c.prodotto = p.id 
        WHERE c.consumatore = :email
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([':email' => $userEmail]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Carica template item
    $itemTemplatePath = __DIR__ . '/templates/cart_item_template.html';
    // Fallback se il file non esiste ancora
    $defaultTemplate = '<div class="cart-item"><strong>{{NOME}}</strong> - Qty: {{QTY}}</div>';
    $itemTemplate = file_exists($itemTemplatePath) ? file_get_contents($itemTemplatePath) : $defaultTemplate;

    if (count($items) > 0) {
        foreach ($items as $item) {
            // Se non hai recuperato lo stock dalla query, impostiamo un default per evitare errori PHP
            if (!isset($item['stock'])) $item['stock'] = 100;

            $cartItemsHtml .= renderCartItem($item, $itemTemplate);
            $totalAmount += $item['subtotal'];
            $totalItems += $item['quantita'];
        }
    } else {
        $cartItemsHtml = '<div class="empty-cart"><h2>Il tuo carrello è vuoto</h2><a href="./shop.php" class="btn-join">Vai allo Shop</a></div>';
    }

} catch (PDOException $e) {
    $cartItemsHtml = '<div class="error-msg">Errore caricamento carrello.</div>';
    error_log("Cart GET Error: " . $e->getMessage());
}

// Gestione messaggi sessione
$userFeedback = '';
if (isset($_SESSION['msg_content'])) {
    $msgClass = ($_SESSION['msg_type'] == 'success') ? 'success-msg' : 'error-msg';
    $userFeedback = '<div class="alert show ' . $msgClass . '">' . htmlspecialchars($_SESSION['msg_content']) . '</div>';
    unset($_SESSION['msg_type']);
    unset($_SESSION['msg_content']);
}

// 4. CARICAMENTO HTML
$htmlContent = file_get_contents('./cart.html');

$replacements = [
    '{{TOTAL_ITEMS}}' => $totalItems,
    '{{TOTAL_AMOUNT}}' => '€' . number_format($totalAmount, 2, ',', '.'),
    '{{CART_CONTENT}}' => $cartItemsHtml,
    '{{USER_FEEDBACK}}' => $userFeedback,
    '{{HIDDEN_IF_EMPTY}}' => ($totalItems === 0) ? 'hidden' : ''
];

echo str_replace(array_keys($replacements), array_values($replacements), $htmlContent);
?>