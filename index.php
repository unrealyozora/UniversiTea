<?php
session_start();

$template = file_get_contents('src/pages/templates/home.html');

$user_action = '';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Utente loggato: Mostra icona profilo
    $user_action = '<li>
                        <a href="src/pages/dashboard.php" class="nav-text-link" aria-label="Visualizza il tuo profilo">
                            <img src="assets/images/user.png" alt="" aria-hidden="true">
                            <span class="mobile-only-text">Profilo</span>
                        </a>
                    </li>';

    // Utente loggato: Bottone per controllare i punti
    $loyalty_action = '<a href="src/pages/dashboard.php" class="btn-loyalty">Controlla i tuoi punti</a>';
} else {
    // Utente ospite: Mostra bottone accedi
    $user_action = '<li>
                        <a href="src/pages/register.php" class="btn-join" aria-label="Accedi ora al tuo account">
                            Accedi Ora
                        </a>
                    </li>';

    // Utente ospite: Bottone per iniziare a raccogliere punti
    $loyalty_action = '<a href="src/pages/register.php" class="btn-loyalty">Inizia a raccogliere</a>';
}

$output = str_replace('{{USER_ACTION}}', $user_action, $template);
echo str_replace('{{LOYALTY_ACTION}}', $loyalty_action, $output);
?>