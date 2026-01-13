<?php
session_start();
$error = $_SESSION['registration_error'] ?? '';

unset($_SESSION['registration_error']);

$template = file_get_contents('templates/register.html');

$error_html = '';
if (!empty($error)) {
    $error_escaped = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    $error_html .= '<div class="error-msg" role="alert" data-server-error>' .
        $error_escaped . '</div>';
}

$replacements = [
    '{{ERROR_MESSAGE}}' => $error_html,
];

$output = str_replace(array_keys($replacements), array_values($replacements), $template);
header('Content-type: text/html; charset=utf-8');
echo $output;
?>