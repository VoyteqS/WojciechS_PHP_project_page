<?php
// Połączenie z bazą danych
$mysqli = new mysqli("localhost", "root", "", "shop");

// Sprawdzenie połączenia
if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}

// Obsługa formularza rejestracji
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sprawdzenie, czy użytkownik o podanej nazwie już istnieje
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        echo "Użytkownik o podanej nazwie już istnieje.";
    } else {
        // Haszowanie hasła przed zapisem do bazy danych
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Dodanie nowego użytkownika do bazy danych
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
        if ($mysqli->query($query) === TRUE) {
            echo "Rejestracja zakończona sukcesem. Możesz się zalogować.";
        } else {
            echo "Błąd rejestracji: " . $mysqli->error;
        }
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="styles_login.css?ts=<?=time()?>" />
</head>
<body>
    <div class="login">
        <h1>Rejestracja</h1>
        <form method="post" action="">
            <label>Nazwa użytkownika:</label>
            <input type="text" name="username" required><br><br>
            <label>Hasło:</label>
            <input type="password" name="password" pattern="(?=.*\d)(?=.*[A-Z]).{8,}" title="Hasło musi zawierać co najmniej 8 znaków, przynajmniej jedną wielką literę i przynajmniej jedną cyfrę" required><br><br>
            <input type="submit" name="register" value="Zarejestruj">
        </form>
        <p><a href="login.php">Masz już konto? Zaloguj się tutaj!</a></p>
    </div>
</body>
</html>
