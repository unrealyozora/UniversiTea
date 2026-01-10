<?php
require_once '../config/check_auth.php';
requireAuth('login.html');
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Il tuo profilo</title>
    <meta name="description"
          content="Pagina dedicata al tuo profilo Universitea.">
    <meta name="keywords" content="tè, università, tisane,profilo, negozio">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/print.css" media="print">
</head>
<body>
<header class="main-header">

    <div class="logo">
        <a href="../../index.html">
            <img src="../../assets/images/universitea_logo.svg" alt="UniversiTea - Torna alla Home" class="logo-img">
        </a>
    </div>

    <nav id="main-menu" aria-label="Menu principale">
        <div class="main-menu-pill"></div>
        <ul>
            <li><a href="shop.html"><span lang="en-GB">Shop</span></a></li>
            <li><a href="tea-info.html">Il nostro Tè</a></li>
            <li><a href="about.html"><span lang="en-GB">About</span></a></li>
        </ul>
    </nav>

    <nav id="user-actions" aria-label="Menu utente">
        <ul>
            <li><a href="cart.html" class="cart-link" aria-label="Visualizza carrello"><img
                            src="../../assets/images/shopping-cart.png" alt="Carrello" width="24" height="24"></a></li>
            <li><a href="login.html" class="btn-join" aria-label="Accedi al profilo">Join Now</a></li>
        </ul>
    </nav>

</header>
<div class="dashboard-card">
    <h1>Il Tuo Profilo</h1>

    <div class="status-badge">
        <span class="status-badge">Account Attivo</span>
    </div>

    <div class="nav-links">
        <form action="../config/logout.php" method="POST">
            <button type="submit" name='logout' class="submit-btn">Logout</button>
        </form>
    </div>
    <div class="info-row">
        <span class="info-label">Username:</span>
        <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
    </div>

    <div class="info-row">
        <span class="info-label">Email:</span>
        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
    </div>

    <div class="session-info">
        <strong>📊 Info Sessione:</strong><br>
        Session ID: <?php echo session_id(); ?><br>
        Ultima attività: <?php echo date('d/m/Y H:i:s', $_SESSION['last_activity'] ?? time()); ?><br>
        Timeout: 30 minuti di inattività
    </div>
    <footer>
        <p><a href="https://validator.w3.org/nu/"><img src="https://www.w3.org/Icons/valid-xhtml10" alt="HTML Valido!"></a>
        </p>
        <p>Copyright© 2025 by PCMS - <span lang="en-GB">All rights reserved</span>. Tutti i prodotti presentati sono
            frutto
            della nostra immaginazione e ogni riferimento a persone o cose realmente esistenti è casuale.</p>
        <ul>
            <li>Email: <a href="mailto:universitea@gmail.com">universitea@gmail.com</a></li>
            <li>Telefono: <a href="tel:+3912312390123">+39 12312390123</a></li>
        </ul>
        <p><a href="http://jigsaw.w3.org/css-validator/check/referer"><img
                        src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valido!"></a></p>
    </footer>
</body>
</html>