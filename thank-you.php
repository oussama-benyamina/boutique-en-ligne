<?php
session_start();
require 'functions/db_conn.php';
require 'functions/email_functions.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to log messages
function logMessage($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'thank_you_debug.log');
}

logMessage("Thank you page accessed");

// Check if order_id is provided in the URL and user is logged in
if (!isset($_GET['order_id']) || !isset($_SESSION['client_id'])) {
    logMessage("Invalid access attempt: missing order_id or user not logged in");
    header('Location: index.php');
    exit;
}

$order_id = intval($_GET['order_id']);
$client_id = $_SESSION['client_id'];

logMessage("Processing order ID: $order_id for client ID: $client_id");

// Fetch order details
$order_sql = "SELECT o.*, c.first_name, c.last_name, c.email FROM orders o 
              JOIN clients c ON o.client_id = c.client_id 
              WHERE o.order_id = ? AND o.client_id = ?";
$order_stmt = $pdo->prepare($order_sql);
$order_stmt->execute([$order_id, $client_id]);
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    logMessage("Order not found for order ID: $order_id and client ID: $client_id");
    header('Location: index.php');
    exit;
}

logMessage("Order found: " . json_encode($order));

// Fetch order items
$items_sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE oi.order_id = ?";
$items_stmt = $pdo->prepare($items_sql);
$items_stmt->execute([$order_id]);
$order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

logMessage("Order items fetched: " . count($order_items) . " items");

// Prepare order details for email
$orderDetails = [
    'order_id' => $order_id,
    'total_amount' => $order['total_amount'],
    'items' => $order_items
];

logMessage("Order details prepared for email: " . json_encode($orderDetails));

// Send thank you email
logMessage("Attempting to send thank you email to: " . $order['email']);
$emailSent = sendThankYouEmail($order['email'], $orderDetails);

if ($emailSent) {
    logMessage("Thank you email sent successfully");
} else {
    logMessage("Failed to send thank you email");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <title>Thank You for Your Order - Y nextwatch</title>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <!-- breadcrumb-section -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Order Confirmation</p>
                        <h1>Thank You</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <!-- thank you section -->
    <div class="checkout-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="order-details-wrap">
                        <h2>Thank you for your order, <?= htmlspecialchars($order['first_name']) ?>!</h2>
                        <p>Your order has been successfully placed. 
                        <?php if ($emailSent): ?>
                            We've sent a confirmation email to <?= htmlspecialchars($order['email']) ?>.
                        <?php else: ?>
                            There was an issue sending the confirmation email, but your order has been processed successfully.
                        <?php endif; ?>
                        </p>
                        
                        <h3 class="mt-5 mb-4">Order Details</h3>
                        <table class="order-details table table-bordered">
                            <tr>
                                <th>Order ID</th>
                                <td><?= $order['order_id'] ?></td>
                            </tr>
                            <tr>
                                <th>Order Date</th>
                                <td><?= date('F j, Y, g:i a', strtotime($order['order_date'])) ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><?= ucfirst($order['status']) ?></td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                        </table>

                        <h3 class="mt-5 mb-4">Ordered Items</h3>
                        <table class="order-details table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['unit_price'], 2) ?></td>
                                        <td>$<?= number_format($item['quantity'] * $item['unit_price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="mt-5 text-center">
                            <a href="my-orders.php" class="boxed-btn">View All Orders</a>
                            <a href="index.php" class="boxed-btn">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end thank you section -->

    <?php include 'includes/_footer.php'; ?>
</body>
</html>
