<?php
session_start();
if ($_SESSION["logged_in"] && isset($_SESSION["username"])) {
    $redirect_to = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
    unset($_SESSION['redirect_after_login']);

    header("Location: " . $redirect_to);
    exit();
}
$error = $_SESSION['login_error'] ?? '';
$error_type = $_SESSION['error_type'] ?? '';
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
    '{{GENERIC_ERROR}}' => '',
    '{{USERNAME_VALUE}}' => $username_escaped,
];

$error_map = [
    'all' => '{{GENERIC_ERROR}}',
];

if (isset($error_map[$error_type])) {
    $replacements[$error_map[$error_type]] = $error_html;
}

$output = str_replace(array_keys($replacements), array_values($replacements), $template);
header('Content-type: text/html; charset=utf-8');
echo $output;
?>

