<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany i jest administratorem lub moderatorem
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Połączenie z bazą danych
$mysqli = new mysqli("localhost", "root", "", "shop");

// Sprawdzenie połączenia
if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}

$username = $_SESSION['username'];

// Pobranie typu konta użytkownika z bazy danych
$query = "SELECT account_type FROM users WHERE username = '$username'";
$result = $mysqli->query($query);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $account_type = $row['account_type'];

    // Sprawdzenie typu konta
    if ($account_type !== 'admin' && $account_type !== 'moderator') {
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}

// Pobranie dostępnych kategorii z bazy danych
$categoryQuery = "SELECT id, name FROM categories";
$categoryResult = $mysqli->query($categoryQuery);

// Dodawanie produktu
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $price = $_POST['price'];

    // Przeniesienie przesłanego pliku na serwer
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    // Wstawienie produktu do bazy danych
    $query = "INSERT INTO products (name, category, description, image, price) VALUES ('$name', '$category', '$description', '$image', '$price')";
    if ($mysqli->query($query) === TRUE) {
        echo "Produkt został dodany.";
    } else {
        echo "Błąd dodawania produktu: " . $mysqli->error;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dodaj produkt</title>
    <link rel="stylesheet" href="styles_add_products.css?ts=<?=time()?>" />
</head>
<body>
    <div class="add_product">
        <h2><a href="dashboard.php">Dodaj produkt</a></h2>
        <form method="post" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <td><label>Nazwa:</label></td>
                    <td><input type="text" name="name" required></td>
                </tr>
                <tr>
                    <td><label>Kategoria:</label></td>
                    <td>
                        <select name="category" required>
                            <?php while ($row = $categoryResult->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>Opis:</label></td>
                    <td><textarea name="description" required></textarea></td>
                </tr>
                <tr>
                    <td><label>Zdjęcie:</label></td>
                    <td><input type="file" name="image" required></td>
                </tr>
                <tr>
                    <td><label>Cena:</label></td>
                    <td><input type="number" name="price" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="add_product" value="Dodaj produkt"></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>

