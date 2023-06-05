<?php
// Połączenie z bazą danych
$mysqli = new mysqli("localhost", "root", "", "shop");

// Sprawdzenie połączenia
if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}

// Pobranie produktów z bazy danych
$query = "SELECT p.id, p.name, p.description, p.image, p.price, c.name AS category_name FROM products AS p
          INNER JOIN categories AS c ON p.category = c.id";
$result = $mysqli->query($query);

session_start();

// Sprawdzenie, czy użytkownik jest administratorem
$is_admin = isset($_SESSION['admin']);

// Sprawdzenie, czy użytkownik jest moderatorem
$is_moderator = isset($_SESSION['moderator']);

// Dodawanie produktu do koszyka
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Pobranie informacji o produkcie
    $productQuery = "SELECT * FROM products WHERE id = $product_id";
    $productResult = $mysqli->query($productQuery);
    $product = $productResult->fetch_assoc();

    // Dodanie produktu do sesji koszyka
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Sprawdzenie, czy produkt już istnieje w koszyku
    $cart_item_index = -1;
    foreach ($_SESSION['cart'] as $index => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_item_index = $index;
            break;
        }
    }

    if ($cart_item_index >= 0) {
        // Aktualizacja ilości zamawianego produktu
        $_SESSION['cart'][$cart_item_index]['quantity'] += $quantity;
    } else {
        // Dodanie nowego produktu do koszyka
        $_SESSION['cart'][] = array(
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        );
    }

    echo "Produkt został dodany do koszyka.";
}

// Usuwanie produktu
if (isset($_POST['delete_product']) && ($is_admin || $is_moderator)) {
    $product_id = $_POST['product_id'];

    // Usuwanie produktu z bazy danych
    $deleteQuery = "DELETE FROM products WHERE id = $product_id";
    $mysqli->query($deleteQuery);

    echo "Produkt został usunięty.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Produkty</title>
    <link rel="stylesheet" href="styles_products.css?ts=<?=time()?>" />
</head>
<body>
    <div class="products">
        <h2><a href="dashboard.php">Produkty</a></h2>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-tile">
                <h3><?php echo $row['name']; ?></h3>
                <p>Kategoria: <?php echo $row['category_name']; ?></p>
                <p>Opis: <?php echo $row['description']; ?></p>
                <p>Cena: <?php echo $row['price']; ?></p>
                <img src="uploads/<?php echo $row['image']; ?>" alt="Product Image" width="200">
                
                <form method="post" action="">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <label>Ilość:</label>
                    <input type="number" name="quantity" value="1" min="1" required>
                    <input type="submit" name="add_to_cart" value="Dodaj do koszyka">
                </form>

                <?php if ($is_admin || $is_moderator): ?>
                    <form method="post" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" name="delete_product" value="Usuń">
                    </form>
                <?php endif; ?>
            </div>
            <hr>
        <?php endwhile; ?>
    </div>
</body>
</html>


<?php
$mysqli->close();
?>
