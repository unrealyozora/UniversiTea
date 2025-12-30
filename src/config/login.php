<?php
session_start();
header('Content-Type: application/json');

require_once 'database_conn.php';
require_once 'user.php';

$user = new User();

checkMethod('POST');
setUserData();
checkValidData();
loginUser();

function checkMethod($Method): void
{
    if ($_SERVER['REQUEST_METHOD'] != $Method) {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
        exit();
    }
}

function setUserData(): void
{
    global $user;
    $data = json_decode(file_get_contents('php://input'), true);

    //Qui username è l'identificativo dell'utente in base a cosa ha inserito: può essere username o email
    $user->setUsername($data['username'] ?? '');
    $user->setPassword($data['password'] ?? '');
}

function checkValidData(): void
{
    global $user;
    if (empty($user->getUsername()) || empty($user->getPassword())) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Inserire una username e una password']);
        exit();
    }
}

function loginUser(): void
{
    global $user;
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT email, username, password FROM Utente WHERE username = :username or email = :email";
        $stmt = $db->prepare($query);
        $identifier = $user->getUsername();
        $stmt->bindValue(':username', $identifier);
        $stmt->bindValue(':email', $identifier);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Credenziali non valide']);
            exit();
        }

        $result = $stmt->fetch();

        if (!password_verify($user->getPassword(), $result['password'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Credenziali non valide']);
            exit();
        }

        $_SESSION['email'] = $result['email'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['logged_in'] = true;

        session_regenerate_id(true);

        echo json_encode([
            'success' => true,
            'message' => 'Login effettuato',
            'user' => [
                'id' => $result['id'],
                'username' => $result['username'],
                'email' => $result['email']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}