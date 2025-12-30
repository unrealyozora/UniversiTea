<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'database_conn.php';
require_once 'user.php';

$user = new User();

checkMethod('POST');
setUserData();
checkValidData();
checkValidEmail();
try {
    registerUser();
} catch (Exception $e) {
}


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
    $data = json_decode(file_get_contents("php://input"), true);

    $user->setUsername(trim($data["username"] ?? ''));
    $user->setEmail(trim($data["email"] ?? ''));
    $user->setPhone(trim($data["phone"] ?? ''));
    $user->setPassword(trim($data["password"] ?? ''));
    $user->setConfirmPassword(trim($data["confirm_password"] ?? ''));
}

function checkValidData(): void
{
    global $user;

    if (empty($user->getUsername()) || empty($user->getEmail()) || empty($user->getPassword()) || empty($user->getConfirmPassword()) || empty($user->getPhone())) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Inserire i campi obbligatori']);
        exit();
    }
    if (strlen($user->getUsername()) < 3 || strlen($user->getUsername()) > 32) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username minimi 3 caratteri e massimo 32 caratteri']);
        exit();
    }
    if (strlen($user->getPassword()) < 6) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Password minimi 10 caratteri"]);
        exit();
    }
    if ($user->getPassword() != $user->getConfirmPassword()) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Le due password non corrispondono"]);
        exit();
    }
    if ((strlen($user->getPhone()) != 9) && (strlen($user->getPhone()) != 10)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Numero di telefono non valido"]);
        exit();
    }
}

function checkValidEmail(): void
{
    global $user;
    if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email non valida"]);
        exit();
    }
}

/**
 * @throws Exception
 */
function RegisterUser(): void
{
    global $user;
    $user_type = UserType::Compratore->value;
    $start_fid_points = 0;
    $address = "togliereIndirizzo";
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT email FROM Utente WHERE username = :username or email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $user->getUsername());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["success" => false, "message" => "Username  o email già esistente"]);
            exit();
        }

        $pw_hash = password_hash($user->getPassword(), PASSWORD_BCRYPT);

        $query = "INSERT INTO Utente (email,indirizzo, username, telefono, password, punti_fedelta, tipo_utente) VALUES (:email, :indirizzo, :username, :telefono, :password, :start_fid_points, :user_type)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $user->getUsername());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $pw_hash);
        $stmt->bindValue(':telefono', $user->getPhone());
        $stmt->bindValue(':indirizzo', $address);
        $stmt->bindValue(':start_fid_points', $start_fid_points);
        $stmt->bindValue(':user_type', $user_type);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Utente inserito", "user_id" => $db->lastInsertId()]);
            exit();
        } else {
            throw new Exception("Errore durante la registrazione");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}