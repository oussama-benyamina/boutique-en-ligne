<?php
session_start();
require 'functions/db_conn.php'; // Include the database connection
require 'vendor/autoload.php'; // Include the Stripe PHP library

use Stripe\Stripe;

// Set your secret key
Stripe::setApiKey('sk_test_51Q5msRP1qIvvzGGYRrbmtiY0QybxYeoUMZhzr8LRNTr4BOclqzF15QUfDbS0hJQws1P0htM8ziEMEBMSpyLVbA0x00ZleNM23a');

// Retrieve order details from the submitted form
$name = $_POST['name'];
$email = $_POST['email'];
$address = $_POST['address'];
$city = $_POST['city'];
$postal_code = $_POST['postal_code'];
$country = $_POST['country'];
$phone = $_POST['phone'];
$paymentIntentId = $_POST['payment_intent_id'];

// Calculate total amount (ensure the session cart is available)
$totalAmount = 0;
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

// Save the order in the database
try {
    $stmt = $pdo->prepare("INSERT INTO orders (client_id, order_date, status, total_amount, shipping_address, city, postal_code, country) VALUES (?, NOW(), 'Pending', ?, ?, ?, ?, ?)");
    $stmt->execute([1, $finalTotal, $address, $city, $postal_code, $country]); // Assuming client_id is 1 for this example; replace with the real client ID
    $orderId = $pdo->lastInsertId();

    // Save each item to the order_items table
    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $quantity = $_SESSION['cart'][$productId];
        $unitPrice = $item['price'];
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $productId, $quantity, $unitPrice]);
    }

    // Clear the cart session
    unset($_SESSION['cart']);
} catch (Exception $e) {
    echo 'Error saving the order: ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <!-- breadcrumb-section -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Checkout Completed</p>
                        <h1>Thank You</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <!-- checkout complete section -->
    <div class="checkout-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Your order has been placed!</h2>
                    <p>Thank you for your purchase. Your order is being processed.</p>
                    <a href="shop.php" class="boxed-btn">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
    <!-- end checkout complete section -->

    <?php include 'includes/_footer.php'; ?>

    <!-- jquery -->
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <!-- bootstrap -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
