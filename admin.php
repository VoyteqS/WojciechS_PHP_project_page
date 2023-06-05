<?php
// Połączenie z bazą danych
$mysqli = new mysqli("localhost", "root", "", "shop");

// Sprawdzenie połączenia
if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}

// Sprawdzenie, czy użytkownik jest zalogowany jako administrator lub moderator
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['moderator'])) {
    header("Location: login.php"); // Przekierowanie na stronę logowania
    exit();
}

// Sprawdzenie, czy użytkownik jest administratorem
$is_admin = isset($_SESSION['admin']);

// Sprawdzenie, czy użytkownik jest moderatorem
$is_moderator = isset($_SESSION['moderator']);

// Sprawdzenie, czy użytkownik jest administratorem lub moderatorem
if (!$is_admin && !$is_moderator) {
    die("Brak uprawnień dostępu.");
}

// Funkcja sprawdzająca, czy dany użytkownik może zarządzać innymi użytkownikami
function canManageUsers()
{
    global $is_admin, $is_moderator;
    return $is_admin || ($is_moderator && !isAdministrator());
}

// Funkcja sprawdzająca, czy dany użytkownik ma rolę administratora
function isAdministrator()
{
    global $mysqli;
    $user_id = $_SESSION['user_id'];
    $query = "SELECT account_type FROM users WHERE id = $user_id";
    $result = $mysqli->query($query);
    $row = $result->fetch_assoc();
    return $row['account_type'] === 'admin';
}

// Dodawanie użytkownika
if (isset($_POST['add_user']) && canManageUsers()) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $account_type = $_POST['account_type'];

    // Sprawdzenie, czy użytkownik ma rolę moderatora
    if ($is_moderator) {
        $account_type = 'user'; // Moderator może tworzyć tylko konta użytkowników
    }

    // Szyfrowanie hasła
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Wstawianie użytkownika do bazy danych
    $insertQuery = "INSERT INTO users (username, password, account_type) VALUES ('$username', '$hashed_password', '$account_type')";
    $mysqli->query($insertQuery);

    echo "Użytkownik został dodany.";
}

// Usuwanie użytkownika
if (isset($_POST['delete_user']) && canManageUsers()) {
    $user_id = $_POST['user_id'];

    // Sprawdzenie, czy użytkownik ma rolę administratora
    if (isAdministrator() && $_SESSION['user_id'] == $user_id) {
        die("Nie można usunąć własnego konta.");
    }

    // Usuwanie użytkownika z bazy danych
    $deleteQuery = "DELETE FROM users WHERE id = $user_id";
    $mysqli->query($deleteQuery);

    echo "Użytkownik został usunięty.";
}

// Aktualizacja użytkownika
if (isset($_POST['update_user']) && canManageUsers()) {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $account_type = $_POST['account_type'];

    // Sprawdzenie, czy użytkownik ma rolę administratora
    if (isAdministrator() && $_SESSION['user_id'] == $user_id) {
        die("Nie można zmienić danych własnego konta.");
    }

    // Sprawdzenie, czy użytkownik ma rolę moderatora
    if ($is_moderator) {
        $account_type = 'user'; // Moderator może zmieniać tylko dane użytkowników
    }

    // Aktualizacja danych użytkownika w bazie danych
    $updateQuery = "UPDATE users SET account_type = '$account_type'";

    // Aktualizacja nazwy użytkownika, jeśli została podana
    if (!empty($new_username)) {
        $updateQuery .= ", username = '$new_username'";
    }

    // Aktualizacja hasła użytkownika, jeśli zostało podane
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updateQuery .= ", password = '$hashed_password'";
    }

    $updateQuery .= " WHERE id = $user_id";

    $mysqli->query($updateQuery);

    echo "Dane użytkownika zostały zaktualizowane.";
}

// Pobranie listy użytkowników
$query = "SELECT id, username, account_type FROM users";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel administracyjny</title>
    <link rel="stylesheet" href="styles_admin.css?ts=<?=time()?>" />
</head>
<body>
    <div class="admin_panel">
        <h1><a href="dashboard.php">Panel administracyjny</a></h1>
        <?php if ($is_admin || $is_moderator): ?>
            <h3>Dodaj użytkownika</h3>
            <form method="post" action="">
                <label>Nazwa użytkownika:</label>
                <input type="text" name="username" required>
                <label>Hasło:</label>
                <input type="password" name="password" required>
                <input type="hidden" name="account_type" value="user"><br>
                <input type="submit" name="add_user" value="Dodaj użytkownika">
            </form>
        <?php endif; ?>

        <h3>Usuń użytkownika</h3>
        <form method="post" action="">
            <label>Wybierz użytkownika:</label>
            <div class="select-container">
                <select name="user_id" required>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php if ($is_admin || ($is_moderator && $row['account_type'] !== 'admin' && $_SESSION['user_id'] != $row['id'])): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>
            </div>
            <input type="submit" name="delete_user" value="Usuń użytkownika">
        </form>

        <h3>Zaktualizuj dane użytkownika</h3>
        <form method="post" action="">
            <label>Wybierz użytkownika:</label>
            <div class="select-container">
                <select name="user_id" required>
                    <?php mysqli_data_seek($result, 0); // Powrót do początku wyników ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php if ($is_admin || ($is_moderator && $row['account_type'] !== 'admin' && $_SESSION['user_id'] != $row['id'])): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>
            </div>
            <label>Nowa nazwa użytkownika*:</label>
            <input type="text" name="new_username">
            <label>Nowe hasło*:</label>
            <input type="password" name="new_password">
            <?php if ($is_admin): ?>
                <label>Typ konta:</label>
                <div class="select-container">
                    <select name="account_type" required>
                        <option value="admin">Administrator</option>
                        <option value="moderator">Moderator</option>
                        <option value="user">Użytkownik</option>
                    </select>
                </div>
            <?php endif; ?>
            <input type="submit" name="update_user" value="Zaktualizuj dane użytkownika">
        </form>

        <h3>Lista użytkowników</h3>
        <table>
            <tr>
                <th>Nazwa użytkownika</th>
                <th>Rola</th>
                <th>Akcja</th>
            </tr>
            <?php mysqli_data_seek($result, 0); // Powrót do początku wyników ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <a href="?user_id=<?php echo $row['id']; ?>">
                            <?php echo $row['username']; ?>
                        </a>
                    </td>
                    <td><?php echo $row['account_type']; ?></td>
                    <td>
                        <?php if ($is_admin || ($is_moderator && $row['account_type'] !== 'admin' && $_SESSION['user_id'] != $row['id'])): ?>
                            <form method="post" action="">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <input type="submit" name="delete_user" value="Usuń">
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php
if (isset($_GET['user_id'])) {
    $selectedUserId = $_GET['user_id'];

    // Pobranie danych wybranego użytkownika
    $userQuery = "SELECT username, account_type FROM users WHERE id = $selectedUserId";
    $userResult = $mysqli->query($userQuery);
    $userRow = $userResult->fetch_assoc();
    $selectedUsername = $userRow['username'];
    $selectedAccountType = $userRow['account_type'];

    // Pobranie zamówień wybranego użytkownika
    $ordersQuery = "SELECT * FROM orders WHERE user_id = $selectedUserId";
    $ordersResult = $mysqli->query($ordersQuery);

    if (isset($_POST['update_status'])) {
        $orderId = $_POST['order_id'];
        $newStatus = $_POST['status'];

        // Aktualizacja statusu zamówienia w bazie danych
        $updateQuery = "UPDATE orders SET status = '$newStatus' WHERE id = $orderId";
        $updateResult = $mysqli->query($updateQuery);

        if ($updateResult) {
            // Powodzenie aktualizacji
            echo "Status zamówienia został zaktualizowany.";
        } else {
            // Błąd aktualizacji
            echo "Wystąpił błąd podczas aktualizacji statusu zamówienia.";
        }
    }
?>
    <h3>Zamówienia użytkownika: <?php echo $selectedUsername; ?></h3>
    <table>
        <tr>
            <th>ID zamówienia</th>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>Miasto</th>
            <th>Ulica</th>
            <th>Kod pocztowy</th>
            <th>Telefon</th>
            <th>Metoda dostawy</th>
            <th>Metoda płatności</th>
            <th>Status</th>
            <th>Aktualizuj</th>
        </tr>
        <?php while ($orderRow = $ordersResult->fetch_assoc()): ?>
            <tr>
                <form method="post" action="">
                    <td><?php echo $orderRow['id']; ?></td>
                    <td><?php echo $orderRow['first_name']; ?></td>
                    <td><?php echo $orderRow['last_name']; ?></td>
                    <td><?php echo $orderRow['city']; ?></td>
                    <td><?php echo $orderRow['street']; ?></td>
                    <td><?php echo $orderRow['postal_code']; ?></td>
                    <td><?php echo $orderRow['phone']; ?></td>
                    <td><?php echo $orderRow['delivery_method']; ?></td>
                    <td><?php echo $orderRow['payment_method']; ?></td>
                    <td>
                        <select name="status">
                            <option value="oczekujący" <?php echo ($orderRow['status'] == 'oczekujący') ? 'selected' : ''; ?>>oczekujący</option>
                            <option value="zrealizowany" <?php echo ($orderRow['status'] == 'zrealizowany') ? 'selected' : ''; ?>>zrealizowany</option>
                            <option value="w trakcie realizacji" <?php echo ($orderRow['status'] == 'w trakcie realizacji') ? 'selected' : ''; ?>>w trakcie realizacji</option>
                            <option value="anulowane" <?php echo ($orderRow['status'] == 'anulowane') ? 'selected' : ''; ?>>anulowane</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="order_id" value="<?php echo $orderRow['id']; ?>">
                        <input type="submit" name="update_status" value="Aktualizuj">
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>
<?php } ?>
</body>
</html>