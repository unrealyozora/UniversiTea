<?php

$nome=$_POST['nome'];
$cognome=$_POST['cognome'];
$username=$_POST['username'];
$email=$_POST['email'];
$password=$_POST['password']; //aggiungere hashing

//AGGIUNGERE CREAZIONE IN DB
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Registrazione Completata</title>
</head>

<body>
<h2>Registrazione completata (TEST)</h2>

<p><strong>Username:</strong> <?php echo htmlspecialchars($username);?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($email);?></p>
<p><strong>Password:</strong> <?php echo htmlspecialchars($password);?></p>
<p><strong>Nome:</strong> <?php echo htmlspecialchars($nome);?></p>
<p><strong>Cognome:</strong> <?php echo htmlspecialchars($cognome);?></p>

<p> L'utente è stato registrato con successo</p>
<a href="../../index.html">Torna alla home</a>
</body>
</html>
