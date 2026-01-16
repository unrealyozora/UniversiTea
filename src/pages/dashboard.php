<?php
require_once '../config/auth/check_auth.php';
requireAuth('login.php');
$user = getCurrentUser();
$template = file_get_contents("templates/dashboard.html");
$username_escaped = htmlspecialchars($user['username']);
$email_escaped = htmlspecialchars($user['email']);
$last_activity = htmlspecialchars(date('d/m/Y H:i:s', $_SESSION['last_activity'] ?? time()));

$replacements = [
        '{{USERNAME}}' => $username_escaped,
        '{{EMAIL}}' => $email_escaped,
        '{{LAST_ACTIVITY}}' => $last_activity,
];

$output = str_replace(array_keys($replacements), array_values($replacements), $template);
header('Content-type: text/html; charset=utf-8');
echo $output;
?>