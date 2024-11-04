<?php
session_start();
require 'functions/db_conn.php'; // Include the database connection
require 'vendor/autoload.php'; // Include Stripe libraries

use Stripe\Stripe; //stripe

// Set your secret key
Stripe::setApiKey('sk_test_51Q5msRP1qIvvzGGYRrbmtiY0QybxYeoUMZhzr8LRNTr4BOclqzF15QUfDbS0hJQws1P0htM8ziEMEBMSpyLVbA0x00ZleNM23a');

// Retrieve form data
$name = $_POST['name'];
$email = $_POST['email'];
$address = $_POST['address'];
$city = $_POST['city'];
$postal_code = $_POST['postal_code'];
$country = $_POST['country'];
$paymentIntentId = $_POST['payment_intent_id'];

// Calculate the total amount
$totalAmount = 0;
$cartItems = [];

if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $sql = "SELECT * FROM products WHERE product_id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_keys($_SESSION['cart']));
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cartItems as $item) {
        $totalAmount += $item['price'] * $_SESSION['cart'][$item['product_id']];
    }
}

$shippingCost = 50; // Fixed shipping cost
$finalTotal = $totalAmount + $shippingCost;

// Insert order into the database
try {
    $stmt = $pdo->prepare("INSERT INTO orders (client_id, order_date, status, total_amount, shipping_address, city, postal_code, country) VALUES (?, NOW(), 'Pending', ?, ?, ?, ?, ?)");
    $stmt->execute([1, $finalTotal, $address, $city, $postal_code, $country]); // Assuming client_id is 1 for this example; replace with the real client ID.
    $orderId = $pdo->lastInsertId();

    // Insert each product into order_items
    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $quantity = $_SESSION['cart'][$productId];
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $productId, $quantity, $item['price']]);
    }

    // Clear the cart session
    unset($_SESSION['cart']);

    // Redirect to checkout complete
    header('Location: checkout-complete.php');
    exit;

} catch (Exception $e) {
    echo 'Error processing your order: ' . $e->getMessage();
    exit;
}
?>
