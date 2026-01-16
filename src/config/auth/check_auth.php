<?php

if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn()
{
    if (isset($_SESSION['username']) && isset($_SESSION['logged_in'])) {
        return true;
    } else {
        return false;
    }
}

function getCurrentUser()
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? ''
    ];
}

function requireAuth($redirectUrl = '../../pages/login.html')
{
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirectUrl);
        exit();
    }
}

function preventAuthAccess($redirectUrl = '../../../index.html')
{
    if (!isLoggedIn()) {
        header('Location: ' . $redirectUrl);
        exit();
    }
}

function regenerateSession()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

function destroySession()
{
    $_SESSION = array();

    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

function isSessionExpired($timeout = 1800)
{
    if (isset($_SESSION['last_activity'])) {
        $elapsed = time() - $_SESSION['last_activity'];
        if ($elapsed > $timeout) {
            return true;
        }
    }
    $_SESSION['last_activity'] = time();
    return false;
}

if (isLoggedIn() && isSessionExpired()) {
    destroySession();
    header('Location: login.php?timeout=1');
    exit();
}