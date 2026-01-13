<?php

use JetBrains\PhpStorm\NoReturn;

session_start();

if (!isset($_SESSION['username']) || !$_SESSION['logged_in']) {
    header('Location: ../../index.html');
    exit();
}

checkRequest();
logout();

function checkRequest(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['logout'])) {
        header('Location: ../../index.html');
        exit();
    }
}

function logout(): void
{
    $_SESSION = array();

    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    session_destroy();

    header('Location: ../pages/login.php');
    exit();
}

