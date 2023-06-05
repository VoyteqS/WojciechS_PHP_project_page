<?php
session_start();

// Sprawdzenie, czy użytkownik jest już zalogowany
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Przekierowanie na stronę główną po zalogowaniu
    exit();
}

// Połączenie z bazą danych
$mysqli = new mysqli("localhost", "root", "", "shop");

// Sprawdzenie połączenia
if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}

// Logowanie użytkownika
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Pobranie użytkownika z bazy danych na podstawie nazwy użytkownika
    $query = "SELECT id, username, password, account_type FROM users WHERE username = '$username'";
    $result = $mysqli->query($query);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Sprawdzenie poprawności hasła
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // Sprawdzenie rodzaju konta
            $account_type = $row['account_type'];
            if ($account_type === 'admin' || $account_type === 'moderator') {
                $_SESSION[$account_type] = true;
            }

            header("Location: dashboard.php"); // Przekierowanie na stronę główną po zalogowaniu
            exit();
        }
    }

    $error_message = "Nieprawidłowa nazwa użytkownika lub hasło.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="styles_login.css?ts=<?=time()?>" />
</head>
<body>
    <div class="login">
        <h1>Logowanie</h1>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label>Nazwa użytkownika:</label>
            <input type="text" name="username" required>
            <label>Hasło:</label>
            <input type="password" name="password" required>
            <input type="submit" name="login" value="Zaloguj się">
        </form>
        <p><a href="register.php">Nie masz konta? Zarejestruj się!</a></p>
    </div>
</body>
</html>


<?php
// Zamknięcie połączenia z bazą danych
$mysqli->close();
?>
