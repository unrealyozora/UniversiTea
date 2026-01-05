<?php
session_start();
header('Content-Type: application/json');

require_once 'check_auth.php';

if (isLoggedIn()) {
    $user = getCurrentUser();

    echo json_encode([
        'success' => true,
        'logged' => true,
        'username' => $user,
        'session' => session_id(),
        'last_activity' => $_SESSION['last_activity'] ?? time(),
    ]);
} else {
    echo json_encode([
        'success' => false,
        'logged' => false,
        'username' => null,
    ]);
}