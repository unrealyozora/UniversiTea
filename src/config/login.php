<?php
// Disabilita la stampa degli errori a video per evitare di rompere il JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Avvia sessione e header JSON
session_start();
header('Content-Type: application/json');

try {
    // Controllo esistenza file prima di includerli
    if (!file_exists('database_conn.php') || !file_exists('user.php')) {
        throw new Exception("File di configurazione mancanti sul server.");
    }

    require_once 'database_conn.php';
    require_once 'user.php';

    // Gestione utente già loggato (senza redirect!)
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        echo json_encode(['success' => true, 'message' => 'Già loggato']);
        exit();
    }

    $user = new User();

    // Lettura input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metodo non consentito");
    }

    $user->setUsername($data['username'] ?? '');
    $user->setPassword($data['password'] ?? '');

    if (empty($user->getUsername()) || empty($user->getPassword())) {
        throw new Exception("Inserisci username e password");
    }

    // Logica Database
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT email, username, password FROM Utente WHERE username = :username OR email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':username', $user->getUsername());
    $stmt->bindValue(':email', $user->getUsername());
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        throw new Exception("Credenziali non valide");
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($user->getPassword(), $result['password'])) {
        throw new Exception("Credenziali non valide");
    }

    // Login Successo
    $_SESSION['email'] = $result['email'];
    $_SESSION['username'] = $result['username'];
    $_SESSION['logged_in'] = true;
    $_SESSION['last_activity'] = time();

    echo json_encode([
        'success' => true,
        'message' => 'Login effettuato'
    ]);

} catch (Exception $e) {
    // Cattura qualsiasi errore e rispondi JSON
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}