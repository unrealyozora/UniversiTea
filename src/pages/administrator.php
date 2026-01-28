<?php
session_start();
require_once '../config/database/database_conn.php';
require_once '../config/shop_functions.php'; // Se hai funzioni utili qui

// 1. CONTROLLO SICUREZZA: Solo gli admin possono vedere questa pagina
// (Adatta 'ruolo' in base alla tua tabella Utenti/Consumatori)
if (!isset($_SESSION['logged_in']) || $_SESSION['tipo_utente'] !== 'Venditore') {
    header('Location: login.php');
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 2. Query per ottenere tutti i prodotti
    // Usiamo una query semplificata rispetto allo shop
    $sql = "SELECT id, nome, prezzo, disponibilita, 
            CASE 
                WHEN id IN (SELECT id FROM Bevande) THEN 'Bevanda'
                WHEN id IN (SELECT id FROM March_Bevande) THEN 'Merch'
                WHEN id IN (SELECT id FROM Servizi) THEN 'Servizio'
                WHEN id IN (SELECT id_bundle FROM Bundle) THEN 'Bundle'
                ELSE 'Altro'
            END as categoria
            FROM Prodotti 
            ORDER BY id DESC"; // I più recenti in alto

    $stmt = $conn->query($sql);
    $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Carica il template della riga
    $rowTemplatePath = __DIR__ . '/templates/admin_row_template.html';
    $rowTemplate = file_exists($rowTemplatePath)
        ? file_get_contents($rowTemplatePath)
        : '<tr><td colspan="6">Template riga mancante</td></tr>';

    $listaHtml = '';

    // 4. Genera le righe della tabella
    foreach ($prodotti as $prodotto) {
        $replacements = [
            '{{ID}}'            => $prodotto['id'],
            '{{NOME}}'          => htmlspecialchars($prodotto['nome']),
            '{{PREZZO}}'        => number_format($prodotto['prezzo'], 2, ',', '.'),
            '{{DISPONIBILITA}}' => $prodotto['disponibilita'],
            '{{CATEGORIA}}'     => $prodotto['categoria']
        ];

        $listaHtml .= str_replace(
            array_keys($replacements),
            array_values($replacements),
            $rowTemplate
        );
    }

} catch (PDOException $e) {
    error_log("Errore Admin: " . $e->getMessage());
    $listaHtml = '<tr><td colspan="6">Errore nel caricamento dati.</td></tr>';
}

// 5. Gestione Messaggi Feedback (es. "Prodotto eliminato")
$userFeedback = '';
if (isset($_SESSION['msg_content'])) {
    $msgClass = ($_SESSION['msg_type'] == 'success') ? 'success-msg' : 'error-msg';
    $userFeedback = '<div class="' . $msgClass . '">' . $_SESSION['msg_content'] . '</div>';
    unset($_SESSION['msg_type'], $_SESSION['msg_content']);
}

// 6. Carica e Renderizza la pagina completa
$pageTemplate = file_get_contents(__DIR__ . '/administrator.html');

echo str_replace(
    ['{{LISTA_PRODOTTI_ADMIN}}', '{{USER_FEEDBACK}}'],
    [$listaHtml, $userFeedback],
    $pageTemplate
);
