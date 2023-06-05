<?php
session_start();
error_reporting(0);

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$is_admin = false; // Domyślnie użytkownik nie jest administratorem
$is_moderator = false; // Domyślnie użytkownik nie jest moderatorem

// Połączenie z bazą danych
$mysqli = new mysqli("localhost", "root", "", "shop");

// Sprawdzenie połączenia
if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}

// Pobranie użytkownika z bazy danych na podstawie nazwy użytkownika
$query = "SELECT account_type FROM users WHERE username = '$username'";
$result = $mysqli->query($query);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $account_type = $row['account_type'];

    // Sprawdzenie rodzaju konta
    if ($account_type === 'admin') {
        $is_admin = true;
    } elseif ($account_type === 'moderator') {
        $is_moderator = true;
    }
}

// Zamknięcie połączenia z bazą danych
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep Internetowy</title>
    <link rel="stylesheet" type="text/css" href="styles.css?ts=<?=time()?>" />
</head>
<body>
    <div id="con">
        <header>
            <div class="header">
                <h1>Sklep Internetowy</h1>
                <h2>Witaj, <?php echo $username; ?></h2>
                <nav>
                    <ul>
                        <li><a href="dashboard.php">Strona główna</a></li>
                        <?php if ($is_admin || $is_moderator): ?>
                            <li><a href="dashboard.php?id_strony=add_product">Dodaj produkty</a></li>
                        <?php endif; ?>
                        <?php if ($is_admin || $is_moderator): ?>
                            <li><a href="admin.php">Admin panel</a></li>
                        <?php endif; ?>
                        <li><a href="dashboard.php?id_strony=products">Produkty</a></li>
                        <li><a href="dashboard.php?id_strony=cart">Koszyk</a></li>
                        <li><a href="logout.php">Wyloguj</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
            <div class="main">
                <section>
                    
                    <?php
                    if(isset($_GET['id_strony'])){
        
                        if($_GET['id_strony']=='dashboard'){
                            include('dashboard.php');
                        }
                        else if($_GET['id_strony']=='add_product'){
                            include('add_product.php');
                        }
                        else if($_GET['id_strony']=='cart'){
                            include('cart.php');
                        }
                        else if($_GET['id_strony']=='products'){
                            include('products.php');
                        }
                    }
                    ?>

                </section>
            </div>
        </main>

        <footer>
            <div>
                <p>&copy; 2023 Sklep Internetowy. Wszelkie prawa zastrzeżone.</p>
            </div>
        </footer>
    </div>
</body>
</html>

