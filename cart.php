<?php
session_start();

// Aktualizacja ilości zamawianego produktu w koszyku
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_item['quantity'] = $quantity;
            break;
        }
    }

    echo "Ilość produktu została zaktualizowana.";
}

// Usunięcie produktu z koszyka
if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];

    foreach ($_SESSION['cart'] as $index => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            unset($_SESSION['cart'][$index]);
            break;
        }
    }

    echo "Produkt został usunięty z koszyka.";
}

// Połączenie z bazą danych
$mysqli = new mysqli("localhost", "root", "", "shop");

// Sprawdzenie połączenia z bazą danych
if ($mysqli->connect_errno) {
    echo "Błąd połączenia z bazą danych: " . $mysqli->connect_error;
    exit();
}

// Przetwarzanie formularza zamówienia
if (isset($_POST['place_order'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $city = $_POST['city'];
    $street = $_POST['street'];
    $postal_code = $_POST['postal_code'];
    $phone = $_POST['phone'];
    $delivery_method = $_POST['delivery_method'];
    $payment_method = $_POST['payment_method'];

    // Sprawdzenie poprawności logowania
    if (!isset($_SESSION['user_id'])) {
        echo "Musisz być zalogowany, aby złożyć zamówienie.";
        exit();
    }

    // Pobranie user_id na podstawie zalogowanego użytkownika
    $user_id = $_SESSION['user_id'];

    // Zapis zamówienia do bazy danych
    $order_status = 'oczekujący';

    $insert_order_query = "INSERT INTO orders (user_id, first_name, last_name, city, street, postal_code, phone, delivery_method, payment_method, status)
                           VALUES ('$user_id', '$first_name', '$last_name', '$city', '$street', '$postal_code', '$phone', '$delivery_method', '$payment_method', '$order_status')";

    if ($mysqli->query($insert_order_query)) {
        $order_id = $mysqli->insert_id;
        foreach ($_SESSION['cart'] as $cart_item) {
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['quantity'];
            $price = $cart_item['price'];

            $insert_order_items_query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                                         VALUES ('$order_id', '$product_id', '$quantity', '$price')";

            $mysqli->query($insert_order_items_query);
        }

        // Usunięcie zawartości koszyka po złożeniu zamówienia
        $_SESSION['cart'] = array();

        echo "Twoje zamówienie zostało złożone.";
    } else {
        echo "Wystąpił błąd podczas zapisywania zamówienia: " . $mysqli->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Koszyk</title>
    <link rel="stylesheet" type="text/css" href="styles_cart.css?ts=<?=time()?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // Funkcja do aktualizacji ogólnej ceny przy zmianie ilości produktu
        function updateTotalPrice() {
            var total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).text());
            });
            $('#total-price').text(total.toFixed(2));
        }

        $(document).ready(function() {
            // Aktualizacja ogólnej ceny przy załadowaniu strony
            updateTotalPrice();

            // Aktualizacja ogólnej ceny przy zmianie ilości produktu
            $('body').on('change', '.quantity-input', function() {
                var quantity = $(this).val();
                var price = parseFloat($(this).closest('tr').find('.price').text());
                var subtotal = quantity * price;
                $(this).closest('tr').find('.subtotal').text(subtotal.toFixed(2));
                updateTotalPrice();
            });
        });
    </script>
</head>
<body>
    <div class="cart">
        <h2><a href="dashboard.php">Koszyk</a></h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table>
                <tr>
                    <th>Nazwa</th>
                    <th>Cena</th>
                    <th>Ilość</th>
                    <th>Łączna cena</th>
                    <th>Akcje</th>
                </tr>
                <?php foreach ($_SESSION['cart'] as $cart_item): ?>
                    <tr>
                        <td><?php echo $cart_item['name']; ?></td>
                        <td class="price"><?php echo $cart_item['price']; ?></td>
                        <td>
                            <input type="number" name="quantity" class="quantity-input" value="<?php echo $cart_item['quantity']; ?>" min="1" required>
                        </td>
                        <td class="subtotal"><?php echo $cart_item['price'] * $cart_item['quantity']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="product_id" value="<?php echo $cart_item['product_id']; ?>">
                                <input type="submit" name="remove_product" value="Usuń">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Ogólna cena:</strong></td>
                    <td id="total-price" style="font-weight: bold;"></td>
                    <td></td>
                </tr>
            </table>

            <div class="order-form">
                <h2>Formularz zamówienia</h2>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="first_name">Imię:</label>
                        <input type="text" name="first_name" id="first_name" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Nazwisko:</label>
                        <input type="text" name="last_name" id="last_name" required>
                    </div>

                    <div class="form-group">
                        <label for="city">Miasto:</label>
                        <input type="text" name="city" id="city" required>
                    </div>

                    <div class="form-group">
                        <label for="street">Ulica:</label>
                        <input type="text" name="street" id="street" required>
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Kod pocztowy:</label>
                        <input type="text" name="postal_code" id="postal_code" required pattern="\d{2}-\d{3}">
                        <small>Wprowadź kod pocztowy w formacie XX-XXX</small>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon:</label>
                        <input type="text" name="phone" id="phone" required pattern="\d{9}">
                        <small>Wprowadź numer telefonu składający się z 9 cyfr</small>
                    </div>

                    <div class="form-group">
                        <label for="delivery_method">Sposób dostawy:</label>
                        <select name="delivery_method" id="delivery_method">
                            <option value="kurierska">Dostawa kurierska</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Sposób płatności:</label>
                        <select name="payment_method" id="payment_method">
                            <option value="pobranie">Płatność za pobraniem</option>
                        </select>
                    </div>

                    <input type="submit" name="place_order" value="Złóż zamówienie">
                </form>
            </div>

        <?php else: ?>
            <p>Twój koszyk jest pusty.</p>
        <?php endif; ?>
    </div>
</body>
</html>

