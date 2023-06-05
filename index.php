<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = 'Gość';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Strona główna sklepu</title>
</head>
<body>
    <h1><a href="dashboard.php">Witaj w naszym sklepie!</a></h1>
    <p>Zalogowany jako: <?php echo $username; ?></p>

    <?php if (isset($_SESSION['username'])): ?>
        <a href="logout.php">Wyloguj się</a>
    <?php else: ?>
        <a href="login.php">Zaloguj się</a>
    <?php endif; ?>
</body>
</html>
