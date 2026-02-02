<?php
session_start();

$template = file_get_contents('templates/about.html');

$user_action = '';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $user_action = '<li>
                        <a href="dashboard.php" class="nav-profile-link">
                             <img src="../../assets/images/user.png" alt="Il tuo profilo">
                            <span class="mobile-only-text">Profilo</span>
                        </a>
                    </li>';
} else {
    $user_action = '<li>
                        <a href="register.php" class="btn-join" aria-label="Accedi ora al tuo account">
                            Accedi Ora
                        </a>
                    </li>';
}

echo str_replace('{{USER_ACTION}}', $user_action, $template);
?>