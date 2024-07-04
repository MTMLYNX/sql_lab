<?php
session_start();

$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$_SESSION['cart'][$product_id] = $quantity;

echo "Product added to cart. <a href='products.php'>Continue Shopping</a> | <a href='checkout.php'>Checkout</a>";
?>
