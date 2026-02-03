<?php
session_start();
require_once '../config/database/database_conn.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$venditoreBanner = '';
$isSeller = (isset($_SESSION['tipo_utente']) && $_SESSION['tipo_utente'] === 'Venditore');
$isSeller ? $venditoreBanner = $banner = file_get_contents('templates/restriction.html') : '';

$email = $_SESSION['email'];

// Gestione delle azioni POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        switch ($_POST['action']) {
            case 'remove':
                if (isset($_POST['product_id'])) {
                    $stmt = $conn->prepare("DELETE FROM Preferiti WHERE consumatore = :email AND prodotto = :prod");
                    $stmt->execute([':email' => $email, ':prod' => $_POST['product_id']]);

                    $_SESSION['msg_type'] = 'success';
                    $_SESSION['msg_content'] = 'Prodotto rimosso dai preferiti';
                }
                break;

            case 'add_to_cart':
                if (isset($_POST['product_id'])) {
                    $prodId = $_POST['product_id'];

                    // Controlla se il prodotto esiste già nel carrello
                    $stmt = $conn->prepare("SELECT * FROM Carrello WHERE consumatore = :email AND prodotto = :prod");
                    $stmt->execute([':email' => $email, ':prod' => $prodId]);

                    if ($stmt->rowCount() > 0) {
                        // Aggiorna la quantità
                        $update = $conn->prepare("UPDATE Carrello SET quantita = quantita + 1 WHERE consumatore = :email AND prodotto = :prod");
                        $update->execute([':email' => $email, ':prod' => $prodId]);
                    } else {
                        // Inserisci nuovo prodotto
                        $insert = $conn->prepare("INSERT INTO Carrello (consumatore, prodotto, quantita) VALUES (:email, :prod, 1)");
                        $insert->execute([':email' => $email, ':prod' => $prodId]);
                    }

                    $_SESSION['msg_type'] = 'success';
                    $_SESSION['msg_content'] = 'Prodotto aggiunto al carrello!';
                }
                break;

            case 'clear_all':
                $stmt = $conn->prepare("DELETE FROM Preferiti WHERE consumatore = :email");
                $stmt->execute([':email' => $email]);

                $_SESSION['msg_type'] = 'success';
                $_SESSION['msg_content'] = 'Tutti i preferiti sono stati rimossi';
                break;
        }

    } catch (PDOException $e) {
        error_log("Errore DB Preferiti: " . $e->getMessage());
        $_SESSION['msg_type'] = 'error';
        $_SESSION['msg_content'] = 'Errore durante l\'operazione.';
    }

    header('Location: favorites.php');
    exit();
}

// Recupera messaggi dalla sessione
$message = $_SESSION['msg_content'] ?? '';
$messageType = $_SESSION['msg_type'] ?? '';

unset($_SESSION['msg_content']);
unset($_SESSION['msg_type']);

// Carica i preferiti dell'utente
$preferiti = [];
try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT p.id as product_id, p.nome, p.prezzo, p.descrizione
        FROM Preferiti pref
        JOIN Prodotti p ON pref.prodotto = p.id
        WHERE pref.consumatore = :email
    ");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $preferiti = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Errore caricamento preferiti: " . $e->getMessage());
    $message = 'Errore nel caricamento dei preferiti';
    $messageType = 'error';
}

$totalItems = count($preferiti);

// Prepara messaggio HTml
$message_html = '';
if (!empty($message)) {
    $message_escaped = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $messageType_escaped = htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8');
    $message_html = '<div class="alert ' . $messageType_escaped . ' show">' . $message_escaped . '</div>';
}

// Genera HTML items preferiti
$items_html = '';
if (empty($preferiti)) {
    $items_html = '
        <div class="empty-cart">
            <div class="empty-cart-icon"></div>
            <h2>Nessun prodotto nei preferiti</h2>
            <p>Aggiungi i tuoi prodotti preferiti per ritrovarli facilmente!</p>
            <a href="shop.php" class="shop-btn">Vai allo Shop</a>
        </div>
    ';
} else {
    $items_html .= '<div class="cart-items">';

    foreach ($preferiti as $item) {
        $product_id = htmlspecialchars($item['product_id'], ENT_QUOTES, 'UTF-8');
        $nome = htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8');
        $prezzo = number_format($item['prezzo'], 2, ',', '.');
        $descrizione = isset($item['descrizione']) && !empty($item['descrizione'])
            ? '<div class="item-description">' . htmlspecialchars($item['descrizione'], ENT_QUOTES, 'UTF-8') . '</div>'
            : '';

        $items_html .= '
            <div class="cart-item" id="item-' . $product_id . '">
                <div class="item-details">
                    <div class="item-name">' . $nome . '</div>
                    <div class="item-price">€' . $prezzo . '</div>
                    ' . $descrizione . '
                </div>
                <div class="item-actions">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="' . $product_id . '">
                        <button type="submit" class="checkout-btn" style="margin-right: 10px;">
                             Aggiungi al Carrello
                        </button>
                    </form>
                    
                    <form method="POST" style="display: inline;" onsubmit="return confirm(\'Vuoi rimuovere questo prodotto dai preferiti?\');">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product_id" value="' . $product_id . '">
                        <button type="submit" class="remove-btn">
                             Rimuovi
                        </button>
                    </form>
                </div>
            </div>
        ';
    }

    $items_html .= '</div>';

    // Aggiungi summary con azioni
    $items_html .= '
        <div class="cart-summary">
            <div class="checkout-actions">
                <form method="POST" style="display: inline;" onsubmit="return confirm(\'Vuoi rimuovere tutti i prodotti dai preferiti?\');">
                    <input type="hidden" name="action" value="clear_all">
                    <button type="submit" class="clear-cart-btn"> Svuota Preferiti</button>
                </form>
                
                <a href="shop.php" class="shop-btn">🛍️ Continua lo Shopping</a>
            </div>
        </div>
    ';
}

$user_action = '';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $user_action = '<li>
                        <a href="dashboard.php" class="nav-profile-link">
                             <img src="../../assets/images/user.png" alt="Il tuo profilo">
                            <span class="mobile-only-text">Profilo</span>
                        </a>
                    </li>';
} else {
    $user_action = '<li>
                        <a href="register.php" class="btn-join" aria-label="Accedi ora al tuo account">
                            Accedi Ora
                        </a>
                    </li>';
}

$template = file_get_contents('templates/favorites.html');

$replacements = [
    '{{VENDITORE_BANNER}}' => $venditoreBanner,
    '{{TOTAL_ITEMS}}' => $totalItems,
    '{{ITEMS_LABEL}}' => $totalItems === 1 ? 'Prodotto' : 'Prodotti',
    '{{MESSAGE_HTML}}' => $message_html,
    '{{ITEMS_HTML}}' => $items_html,
    '{{USER_ACTION}}' => $user_action,
];

$output = str_replace(array_keys($replacements), array_values($replacements), $template);

header('Content-type: text/html; charset=utf-8');
echo $output;
?>