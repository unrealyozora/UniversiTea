<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
$username = $_SESSION['username'] ?? '';

unset($_SESSION['login_error']);
unset($_SESSION['username']);

$template = file_get_contents('templates/login.html');

$error_html = '';
if (!empty($error)) {
    $error_escaped = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    $error_html .= '<div class="error-msg" role="alert" data-server-error>' .
        $error_escaped . '</div>';
}

$username_escaped = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

$replacements = [
    '{{ERROR_MESSAGE}}' => $error_html,
    '{{USERNAME_VALUE}}' => $username_escaped,
];

$output = str_replace(array_keys($replacements), array_values($replacements), $template);

header('Content-type: text/html; charset=utf-8');
echo $output;
?>

