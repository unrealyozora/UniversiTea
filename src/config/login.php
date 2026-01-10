<?php
require_once('user.php');
require_once 'database_conn.php';
session_start();

//TODO raggruppare in funzione separata
if (isset($_SESSION['username']) && $_SESSION['logged_in']) {
    header('Location: ../../index.html');
    exit();
}

$error = '';
$success = '';

$user = new User();

checkRequest();
setUserData();
checkValidData();
loginUser();


function checkRequest(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
        header('Location: ../../index.html');
        exit();
    }
}

function setUserData(): void
{
    global $user;

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $user->setUsername($username);
    $user->setPassword($password);
}

function checkValidData(): void
{
    global $user;
    if (empty($user->getUsername()) || empty($user->getPassword())) {
        $error = 'Username e password sono obbligatori';
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
            $error = 'Credenziali non valide';
            header('Location: ../../index.html');
            exit();
        }

        $result = $stmt->fetch();

        if (!password_verify($user->getPassword(), $result['password'])) {
            $error = 'Credenziali non valide';
            exit();
        }

        $_SESSION['email'] = $result['email'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();

        session_regenerate_id(true);

        header('Location: ../../index.html');
        exit();

    } catch (Exception $e) {
        $error = 'Errore del server: ' . $e->getMessage();
    }
}

?>