<?php
require_once '../config/auth/check_auth.php';
requireAuth('login.php');

$user = getCurrentUser();
$template = file_get_contents("templates/dashboard.html");
$username_escaped = htmlspecialchars($user['username']);
$email_escaped = htmlspecialchars($user['email']);
$fidelity_points = getFidelityPoints();
$last_activity = htmlspecialchars(date('d/m/Y H:i:s', $_SESSION['last_activity'] ?? time()));

function getFidelityPoints(): string
{
    global $user;
    require_once('../config/database/database_conn.php');
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT punti_fedelta FROM Utente WHERE username = :username or email = :email";
        $stmt = $db->prepare($query);
        $username = $user['username'];
        $email = $user['email'];
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch();

    } catch (Exception $e) {
    }
    return $result["punti_fedelta"];
}

$replacements = [
    '{{USERNAME}}' => $username_escaped,
    '{{EMAIL}}' => $email_escaped,
    '{{LAST_ACTIVITY}}' => $last_activity,
    '{{PUNTI}}' => $fidelity_points,
];

$output = str_replace(array_keys($replacements), array_values($replacements), $template);
header('Content-type: text/html; charset=utf-8');
echo $output;
?>