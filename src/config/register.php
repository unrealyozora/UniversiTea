<?php
require_once 'database_conn.php';
require_once 'user.php';

$error = '';
$success = '';
$user = new User();

checkRequest();
setUserData();
checkValidData();
checkValidEmail();
try {
    registerUser();
} catch (Exception $e) {
}


function checkRequest(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['register'])) {
        //header('Location: ../../index.html');
        exit();
    }
}

function setUserData(): void
{
    global $user;

    $user->setUsername(trim($_POST['username'] ?? ''));
    $user->setEmail(trim($_POST['email'] ?? ''));
    $user->setPhone(trim($_POST['phone'] ?? ''));
    $user->setPassword(trim($_POST['password'] ?? ''));
    $user->setConfirmPassword(trim($_POST['confirm_password'] ?? ''));
}

function checkValidData(): void
{
    global $user;

    if (empty($user->getUsername()) || empty($user->getEmail()) || empty($user->getPassword()) || empty($user->getConfirmPassword()) || empty($user->getPhone())) {
        $error = 'Inserire tutti i campi obbligatori';
        exit();
    }
    if (strlen($user->getUsername()) < 3 || strlen($user->getUsername()) > 32) {
        $error = 'Username deve essere compreso tra 3 e 32 caratteri';
        exit();
    }
    if (strlen($user->getPassword()) < 6 || strlen($user->getPassword()) > 32) {
        $error = "La password deve essere compresa tra 6 e 32 caratteri";
        exit();
    }
    if ($user->getPassword() != $user->getConfirmPassword()) {
        $error = "Le due password non corrispondono";
        exit();
    }
    if ((strlen($user->getPhone()) != 9) && (strlen($user->getPhone()) != 10)) {
        $error = "Numero di telefono non valido";
        exit();
    }
}

function checkValidEmail(): void
{
    global $user;
    if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
        $error = "Email non valida";
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
            $error = "Utente già esistente";
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
            $success = "Utente registrato con successo";
            header('Location: ../../index.html');
            exit();
        } else {
            throw new Exception("Errore durante la registrazione");
        }
    } catch (Exception $e) {
        $error = 'Errore del server: ' . $e->getMessage();
    }
}

?>