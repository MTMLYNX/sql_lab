<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
// Oturum kontrolü yapın
//if (!isset($_SESSION['username'])) {
    // Eğer oturum açılmamışsa, kullanıcıyı login.php sayfasına yönlendirin
  //  header("Location: login.php");
    //exit;
//}

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

$db = new Database();

// Ürünleri listeleme işlemi
$sql_products = "SELECT * FROM products";
$stmt_products = $db->pdo->query($sql_products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
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
        .product {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .product img {
            width: 100px;
            height: 100px;
            margin-right: 10px;
            border-radius: 8px;
        }
        .product .info {
            display: inline-block;
            vertical-align: top;
        }
        .logout-link {
            margin-top: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <div class="logout-link"><a href="login.php">Logout</a></div>
        <h3>Products List</h3>
        <?php
        while ($row = $stmt_products->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='product'>";
            echo "<img src='" . $row['image'] . "' alt='" . $row['name'] . "'><br>";
            echo "Product: " . $row['name'] . " - Price: $" . $row['price'] . "<br>";
            echo "<form method='post' action='add_to_cart.php'>
                    <input type='hidden' name='product_id' value='" . $row['id'] . "'>
                    <input type='number' name='quantity' value='1' min='1'>
                    <input type='submit' value='Add to Cart'>
                  </form>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
