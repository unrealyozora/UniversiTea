<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'database_conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message'=>'Metodo non consentito']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? '');
$email = trim($data["email"]?? '');
$password = trim($data["password"] ?? '');
$confirm_password = trim($data["confirm_password"] ?? '');
$birth_date=$data["birthDate"];


if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message'=>'Inserire i campi obbligatori']);
    exit();
}


if (strlen($username) < 3 || strlen($username) > 32) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message'=>'Username minimi 3 caratteri e massimo 32 caratteri']);
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["success" => false, "message"=>"Password minimi 10 caratteri"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message"=>"Email non valida"]);
    exit();
}

if ($password != $confirm_password) {
    http_response_code(400);
    echo json_encode(["success" => false, "message"=>"Le due password non corrispondono"]);
    exit();
}

//TODO Aggiungere check data di nascita?

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT id FROM users WHERE username = :username or email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["success" => false, "message"=>"Username  o email già esistente"]);
        exit();
    }

    $pw_hash = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, email, password, data_nascita) VALUES (:username, :email, :password, :birth_date)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $pw_hash);
    $stmt->bindParam(':birth_date', $birth_date);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["success" => true, "message"=>"Utente inserito", "user_id" => $db->lastInsertId()]);
        exit();
    } else {
        throw new Exception("Errore durante la registrazione");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message"=>$e->getMessage()]);
}




