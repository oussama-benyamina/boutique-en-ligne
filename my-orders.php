<?php
session_start();
require 'functions/db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: index.php');
    exit;
}

$client_id = $_SESSION['client_id'];

// Fetch all orders for the logged-in user, including the first product image for each order
$order_sql = "SELECT o.*, p.amount as paid_amount, p.payment_date,
              (SELECT pi.image_url 
               FROM order_items oi
               JOIN products pi ON oi.product_id = pi.product_id
               WHERE oi.order_id = o.order_id
               LIMIT 1) as first_product_image
              FROM orders o 
              JOIN payments p ON o.order_id = p.order_id 
              WHERE o.client_id = ? AND o.payment_status = 'Paid'
              ORDER BY o.order_date DESC";

$order_stmt = $pdo->prepare($order_sql);
$order_stmt->execute([$client_id]);
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <title>My Orders</title>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <!-- breadcrumb-section -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>View Your Order History</p>
                        <h1>My Orders</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <!-- my orders section -->
    <div class="cart-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cart-table-wrap">
                        <table class="cart-table">
                            <thead class="cart-table-head">
                                <tr class="table-head-row">
                                    <th class="product-image">Product</th>
                                    <th class="product-name">Order Date</th>
                                    <th class="product-price">Total Amount</th>
                                    <th class="product-quantity">Status</th>
                                    <th class="product-total">Payment Date</th>
                                    <th class="product-total">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="6">You have no orders yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="table-body-row">
                                            <td class="product-image">
                                                <img src="<?= htmlspecialchars($order['first_product_image']) ?>" alt="Product Image" style="width: 100px; height: auto;">
                                            </td>
                                            <td class="product-name"><?= date('F j, Y', strtotime($order['order_date'])) ?></td>
                                            <td class="product-price">$<?= number_format($order['total_amount'], 2) ?></td>
                                            <td class="product-quantity">
                                                Payment: <?= $order['payment_status'] ?><br>
                                                Delivery: <?= $order['status'] ?>
                                            </td>
                                            <td class="product-total"><?= date('F j, Y', strtotime($order['payment_date'])) ?></td>
                                            <td class="product-total">
                                                <a href="order-details.php?order_id=<?= $order['order_id'] ?>" class="boxed-btn">View Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end my orders section -->

    <?php include 'includes/_footer.php'; ?>
</body>
</html>
