<?php
session_start();
require 'functions/db_conn.php';

// Check if user is logged in and order_id is provided
if (!isset($_SESSION['client_id']) || !isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$client_id = $_SESSION['client_id'];
$order_id = intval($_GET['order_id']);

// Fetch order details
$order_sql = "SELECT o.*, c.first_name, c.last_name, c.email, c.phone_number, 
              sa.name as shipping_name, sa.email as shipping_email, sa.address as shipping_address, 
              sa.phone as shipping_phone, sa.city as shipping_city, sa.postal_code as shipping_postal_code, 
              sa.country as shipping_country
              FROM orders o 
              JOIN clients c ON o.client_id = c.client_id 
              JOIN shipping_addresses sa ON o.shipping_address_id = sa.shipping_id
              WHERE o.order_id = ? AND o.client_id = ?";
$order_stmt = $pdo->prepare($order_sql);
$order_stmt->execute([$order_id, $client_id]);
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

// Fetch order items
$items_sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE oi.order_id = ?";
$items_stmt = $pdo->prepare($items_sql);
$items_stmt->execute([$order_id]);
$order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <title>Order Details</title>
    <!-- Add custom styles -->
    <style>
        .main-container {
            max-width: 1200px;
            margin: 1rem auto;
            padding: 1.5rem;
            background: linear-gradient(145deg, #f8fffd, #e6fffa);
            border: 2px solid #2F9985;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(47, 153, 133, 0.1);
        }

        .order-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .order-header {
            grid-column: 1 / -1;
            border-bottom: 2px solid rgba(47, 153, 133, 0.2);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(145deg, #ffffff, #f0fffc);
            padding: 1rem;
            border-radius: 12px;
        }

        .order-section {
            padding: 1rem;
            background: linear-gradient(145deg, #ffffff, #f0fffc);
            border-radius: 12px;
            border: 1px solid rgba(47, 153, 133, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .order-section:hover {
            box-shadow: 0 4px 12px rgba(47, 153, 133, 0.1);
        }

        .order-section h3 {
            color: #2F9985;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(47, 153, 133, 0.2);
            font-size: 1.1rem;
        }

        .status-info p, .shipping-info p {
            margin: 0.25rem 0;
            font-size: 0.95rem;
            color: #1a4f45;
        }

        .items-grid {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .item-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: linear-gradient(145deg, #ffffff, #f0fffc);
            border-radius: 12px;
            border: 1px solid rgba(47, 153, 133, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(47, 153, 133, 0.15);
            background: linear-gradient(145deg, #f0fffc, #e6fff9);
        }

        .item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(47, 153, 133, 0.1);
        }

        .item-details {
            flex: 1;
        }

        .item-details h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: #2F9985;
        }

        .item-details p {
            margin: 0.25rem 0;
            color: #1a4f45;
            font-size: 0.95rem;
        }

        .total-section {
            grid-column: 1 / -1;
            text-align: right;
            padding: 1rem;
            background: linear-gradient(145deg, #2F9985, #268571);
            border-radius: 12px;
            margin-top: 1rem;
            border: 1px solid rgba(47, 153, 133, 0.2);
            color: white;
        }

        .total-section h3 {
            font-size: 1.3rem;
            margin: 0;
            color: white;
        }

        .back-button {
            grid-column: 1 / -1;
            text-align: center;
            margin-top: 1rem;
        }

        .back-button .boxed-btn {
            background: linear-gradient(145deg, #2F9985, #268571);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .back-button .boxed-btn:hover {
            background: linear-gradient(145deg, #268571, #1a5a4d);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(47, 153, 133, 0.2);
        }

        /* Info grid styling */
        .info-grid {
            display: grid;
            gap: 0.5rem;
            background: rgba(47, 153, 133, 0.05);
            padding: 1rem;
            border-radius: 8px;
        }

        .info-grid p {
            margin: 0.25rem 0;
            font-size: 0.95rem;
            display: flex;
            justify-content: space-between;
            color: #1a4f45;
        }

        .info-grid p strong {
            color: #2F9985;
        }

        /* Status badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 500;
            background: linear-gradient(145deg, #2F9985, #268571);
            color: white;
            display: inline-block;
        }

        @media (max-width: 992px) {
            .order-container {
                grid-template-columns: 1fr;
            }
            
            .items-grid {
                grid-template-columns: 1fr;
            }
            
            .main-container {
                margin: 0.5rem;
                padding: 1rem;
            }

            .item-image {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <!-- Simplified breadcrumb -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="breadcrumb-text text-center">
                <h1>Order #<?= $order['order_id'] ?></h1>
                <p><?= date('F j, Y', strtotime($order['order_date'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Updated HTML structure -->
    <div class="main-container">
        <div class="order-container">
            <div class="order-header">
                <h2>Order #<?= $order['order_id'] ?></h2>
                <p>Ordered on: <?= date('F j, Y', strtotime($order['order_date'])) ?></p>
            </div>

            <!-- Order Status Section -->
            <div class="order-section">
                <h3>Order Status</h3>
                <div class="status-info">
                    <p><strong>Current Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
                    <p><strong>Last Updated:</strong> <?= $order['last_updated'] ? date('F j, Y, g:i a', strtotime($order['last_updated'])) : 'N/A' ?></p>
                    <?php if (!empty($order['admin_notes'])): ?>
                        <p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($order['admin_notes'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shipping Information Section -->
            <div class="order-section">
                <h3>Shipping Information</h3>
                <div class="shipping-info">
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['shipping_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['shipping_email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['shipping_phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                    <p><strong>City:</strong> <?= htmlspecialchars($order['shipping_city']) ?></p>
                    <p><strong>Postal Code:</strong> <?= htmlspecialchars($order['shipping_postal_code']) ?></p>
                    <p><strong>Country:</strong> <?= htmlspecialchars($order['shipping_country']) ?></p>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="items-grid">
                <?php foreach ($order_items as $item): ?>
                    <div class="item-card">
                        <img class="item-image" src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-details">
                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                            <p>Quantity: <?= $item['quantity'] ?></p>
                            <p>Price: $<?= number_format($item['unit_price'], 2) ?></p>
                            <p><strong>Subtotal: $<?= number_format($item['quantity'] * $item['unit_price'], 2) ?></strong></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-section">
                <h3>Total: $<?= number_format($order['total_amount'], 2) ?></h3>
            </div>

            <div class="back-button">
                <a href="index.php" class="boxed-btn">Back to Home</a>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>
</body>
</html>
