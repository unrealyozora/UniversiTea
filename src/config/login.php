<?php
session_start();
header('Content-Type: application/json');

require_once 'database_conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Inserire una username e una password']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT id, username, password FROM users WHERE username = :username or email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':email', $username);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Credenziali non valide']);
        exit();
    }

    $user = $stmt->fetch();

    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Credenziali non valide']);
        exit();
    }

    $_SESSION['id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;

    session_regenerate_id(true);

    echo json_encode([
        'success' => true,
        'message' => 'Login effettuato',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ]
    ]);
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>