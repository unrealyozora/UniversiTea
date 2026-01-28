<?php
session_start();
if ($_SESSION["logged_in"] && isset($_SESSION["username"])) {
    header("Location: dashboard.php");
}
$error = $_SESSION['registration_error'] ?? '';
$error_type = $_SESSION['error_type'] ?? '';

unset($_SESSION['registration_error']);

$template = file_get_contents('templates/register.html');

$error_html = '';
if (!empty($error)) {
    $error_escaped = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    $error_html .= '<div class="error-msg" role="alert" data-server-error>' .
        $error_escaped . '</div>';
}

$replacements = [
    '{{GENERIC_ERROR}}' => '',
    '{{USERNAME_ERROR}}' => '',
    '{{PASSWORD_ERROR}}' => '',
    '{{CONFIRM_PWD_ERROR}}' => '',
    '{{PHONE_ERROR}}' => '',
    '{{EMAIL_ERROR}}' => '',
];

$error_map = [
    'all' => '{{GENERIC_ERROR}}',
    'username' => '{{USERNAME_ERROR}}',
    'email' => '{{EMAIL_ERROR}}',
    'password' => '{{PASSWORD_ERROR}}',
    'confirmPassword' => '{{CONFIRM_PWD_ERROR}}',
    'phone' => '{{PHONE_ERROR}}',
];

if (isset($error_map[$error_type])) {
    $replacements[$error_map[$error_type]] = $error_html;
}
$output = str_replace(array_keys($replacements), array_values($replacements), $template);
header('Content-type: text/html; charset=utf-8');
echo $output;
?>