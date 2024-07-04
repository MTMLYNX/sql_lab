<?php
session_start();

class Database {
    private $MYSQL_HOST = "localhost";
    private $MYSQL_USER = 'root';
    private $MYSQL_PASS = '1234';
    private $MYSQL_DB = 'drink_ordering';
    public $pdo = null;

    public function __construct() {
        $SQL = "mysql:host=" . $this->MYSQL_HOST . ";dbname=" . $this->MYSQL_DB;

        try {
            $this->pdo = new \PDO($SQL, $this->MYSQL_USER, $this->MYSQL_PASS);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            die("PDO ile veritabanına ulaşılamadı: " . $e->getMessage());
        }
    }
}

// Create a database instance
$db = new Database();

$success_message = '';
$error_message = '';

// Sepet onaylandıktan sonra işlemleri gerçekleştir
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_cart'])) {
    // Sepet doluysa
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $_SESSION['cart_confirmed'] = true; // Sepet onaylandı işareti
    } else {
        $error_message = "Your cart is empty.";
    }
}
// Kullanıcı ve kart bilgilerini alır ve işlemleri gerçekleştirir
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['card_number'])) {
    $card_number = $_POST['card_number'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Sepet onaylandıysa
    if (isset($_SESSION['cart_confirmed']) && $_SESSION['cart_confirmed'] === true) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $sql = "INSERT INTO orders (card_number, product_id, quantity, name, surname, phone, email, address) VALUES ('$card_number', '$product_id', '$quantity', '$name', '$surname', '$phone', '$email', '$address')";
            try {
                $stmt = $db->pdo->prepare($sql);
                $stmt->execute();
                $success_message = "Order placed successfully!";
            } catch (\PDOException $e) {
                $error_message = "Error: " . $e->getMessage();
            }
        }
        // Sepeti boşalt
        unset($_SESSION['cart']);
        unset($_SESSION['cart_confirmed']);
    } else {
        $error_message = "Please confirm your cart first.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type=text], input[type=email], input[type=submit] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
            color: green;
        }
        .error {
            margin-bottom: 20px;
            color: red;
        }
        .logout-link {
            margin-top: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>
        <div class="logout-link"><a href="logout.php">Logout</a></div>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php if (!isset($_SESSION['cart_confirmed']) || $_SESSION['cart_confirmed'] !== true): ?>
                <h3>Confirm Your Cart</h3>
                <input type="submit" name="confirm_cart" value="Confirm Cart">
            <?php else: ?>
                <h3>Enter User Information</h3>
                <label for="card_number">Card Number:</label><br>
                <input type="text" id="card_number" name="card_number" required><br><br>
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" required><br><br>
                <label for="surname">Surname:</label><br>
                <input type="text" id="surname" name="surname" required><br><br>
                <label for="phone">Phone:</label><br>
                <input type="text" id="phone" name="phone" required><br><br>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" required><br><br>
                <label for="address">Address:</label><br>
                <textarea id="address" name="address" rows="4" required></textarea><br><br>
                <input type="submit" value="Complete Order">
            <?php endif; ?>
        </form>
        <?php if ($success_message): ?>
            <div class="message"><?php echo $success_message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
