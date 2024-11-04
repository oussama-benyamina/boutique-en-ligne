<?php
session_start();
require 'functions/db_conn.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'support' && $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];
    $delivery_status = $_POST['delivery_status'];
    $admin_notes = $_POST['admin_notes'];

    $update_sql = "UPDATE orders SET payment_status = ?, status = ?, admin_notes = ?, last_updated = NOW() WHERE order_id = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$payment_status, $delivery_status, $admin_notes, $order_id]);
}

// Fetch all orders
$sql = "SELECT o.*, c.first_name, c.last_name, c.email,
        p.amount as paid_amount, p.payment_date,
        (SELECT pi.image_url 
         FROM order_items oi
         JOIN products pi ON oi.product_id = pi.product_id
         WHERE oi.order_id = o.order_id
         LIMIT 1) as first_product_image
        FROM orders o 
        JOIN clients c ON o.client_id = c.client_id 
        LEFT JOIN payments p ON o.order_id = p.order_id
        ORDER BY o.order_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <title>Order Management</title>
    <style>
    .delete-btn {
        background-color: #ff0000 !important; /* Red background */
        color: #ffffff !important; /* White text */
    }
    .delete-btn:hover {
        background-color: #cc0000 !important; /* Darker red on hover */
    }

    /* Add view details button style */
    .view-details-btn {
        background-color: #17a2b8 !important; /* Info blue color */
        color: #ffffff !important;
    }
    .view-details-btn:hover {
        background-color: #138496 !important; /* Darker shade for hover */
    }

    .boxed-btn, 
    .btn,
    .delete-btn,
    .view-details-btn,
    .modal-content button {
        border-radius: 50px !important;
    }

    .boxed-btn, 
    .btn,
    .delete-btn,
    .view-details-btn {
        padding: 10px 20px !important;
        margin: 2px !important;
    }
    </style>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['message']) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- breadcrumb-section -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Manage All Orders</p>
                        <h1>Order Management</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <div class="cart-section mt-150 mb-150">
        <div class="container" style="max-width: 1350px;">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cart-table-wrap">
                        <table class="cart-table">
                            <thead class="cart-table-head">
                                <tr class="table-head-row">
                                    <th class="product-image">Product</th>
                                    <th class="product-name">Order ID</th>
                                    <th class="product-price">Customer</th>
                                    <th class="product-quantity">Date</th>
                                    <th class="product-total">Total</th>
                                    <th class="product-remove">Payment Status</th>
                                    <th class="product-remove">Delivery Status</th>
                                    <th class="product-remove">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="table-body-row">
                                        <td class="product-image">
                                            <img src="<?= htmlspecialchars($order['first_product_image']) ?>" alt="Product Image" style="width: 100px; height: auto;">
                                        </td>
                                        <td class="product-name"><?= $order['order_id'] ?></td>
                                        <td class="product-price"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                        <td class="product-quantity"><?= date('F j, Y', strtotime($order['order_date'])) ?></td>
                                        <td class="product-total">$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td class="product-remove"><?= $order['payment_status'] ?></td>
                                        <td class="product-remove"><?= $order['status'] ?></td>
                                        <td class="product-remove">
                                            <button type="button" class="boxed-btn" data-toggle="modal" data-target="#orderModal<?= $order['order_id'] ?>">
                                                Update
                                            </button>
                                            <a href="delete_order.php?order_id=<?= $order['order_id'] ?>" class="boxed-btn delete-btn" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                                            <a href="admin_order_details.php?order_id=<?= $order['order_id'] ?>" class="boxed-btn view-details-btn">View Details</a>
                                        </td>
                                    </tr>

                                    <!-- Modal for each order -->
                                    <div class="modal fade" id="orderModal<?= $order['order_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel<?= $order['order_id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderModalLabel<?= $order['order_id'] ?>">Update Order #<?= $order['order_id'] ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="order_management.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                        <div class="form-group">
                                                            <label for="payment_status">Payment Status</label>
                                                            <select class="form-control" id="payment_status" name="payment_status">
                                                                <option value="Pending" <?= $order['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                                <option value="Paid" <?= $order['payment_status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                                                <option value="Refunded" <?= $order['payment_status'] == 'Refunded' ? 'selected' : '' ?>>Refunded</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="delivery_status">Delivery Status</label>
                                                            <select class="form-control" id="delivery_status" name="delivery_status">
                                                                <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                                <option value="Processing" <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                                                <option value="Shipped" <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                                                <option value="Out for Delivery" <?= $order['status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                                                                <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                                                <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="admin_notes">Admin Notes</label>
                                                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"><?= htmlspecialchars($order['admin_notes'] ?? '') ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="submit" name="update_order" class="boxed-btn">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>
</body>
</html>
