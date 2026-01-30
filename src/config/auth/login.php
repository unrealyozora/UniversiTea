<?php
require_once('../database/database_conn.php');
require_once('user.php');

session_start();

//TODO controllare variabili $error e $success (sono inutilizzate)
$error = '';
$success = '';
$user = new User();

//TODO raggruppare in funzione separata
if (isset($_SESSION['username']) && $_SESSION['logged_in']) {
    header('Location: ../../../index.html');
    exit();
}


checkRequest();
setUserData();
checkValidData();
loginUser();

function checkRequest(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
        header('Location: ../../../index.html');
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
        redirectWithError('Username e password sono obbligatorie', "all");
    }
}

function loginUser(): void
{
    global $user;
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT email, username, password, tipo_utente FROM Utente WHERE username = :username or email = :email";
        $stmt = $db->prepare($query);
        $identifier = $user->getUsername();
        $stmt->bindValue(':username', $identifier);
        $stmt->bindValue(':email', $identifier);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            redirectWithError('Credenziali non valide', "all");
        }

        $result = $stmt->fetch();

        if (!password_verify($user->getPassword(), $result['password'])) {
            redirectWithError('Credenziali non valide', "all");
        }

        $_SESSION['email'] = $result['email'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['tipo_utente'] = $result['tipo_utente'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();

        session_regenerate_id(true);
        if (isset($_SESSION['redirect_after_login'])) {
            $destination = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); // Importante: pulisci la sessione
        } else {
            // Fallback standard se non c'è una pagina di provenienza
            $destination = ($_SESSION['tipo_utente'] === 'Venditore') ? 'administrator.php' : 'dashboard.php';
        }

        header("Location: " . $destination);
        exit();

    } catch (Exception $e) {
        redirectWithError('Si è verificato un errore del server. Riprova più tardi.', "all");
    }
}

function redirectWithError($error, $type): void
{
    $_SESSION['login_error'] = $error;
    $_SESSION['username'] = $_POST['username'] ?? '';
    $_SESSION['error_type'] = $type;
    header('Location: ../../pages/login.php');
    exit();
}

?>
