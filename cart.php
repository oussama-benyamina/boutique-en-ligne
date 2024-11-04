<?php
session_start();
require 'functions/db_conn.php'; // Include the database connection

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if ($product_id && $quantity) {
        $_SESSION['cart'][$product_id] = $quantity;

        // Recalculate total product amount and shipping cost
        $totalProductAmount = 0;
        if (!empty($_SESSION['cart'])) {
            $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
            $sql = "SELECT * FROM products WHERE product_id IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_keys($_SESSION['cart']));
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cartItems as $item) {
                $totalProductAmount += $item['price'] * $_SESSION['cart'][$item['product_id']];
            }
        }

        // Determine shipping cost
        $shippingCost = ($totalProductAmount > 500) ? 0 : 45;

        echo json_encode([
            'success' => true,
            'totalProductAmount' => number_format($totalProductAmount, 2),
            'shippingCost' => number_format($shippingCost, 2)
        ]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

// Handle cart actions: add, remove
$action = isset($_GET['action']) ? $_GET['action'] : '';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action && $product_id) {
    switch ($action) {
        case 'add':
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                $_SESSION['cart'][$product_id] += $quantity;
            }
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
            break;
    }
    // Redirect to avoid form resubmission
    header('Location: cart.php');
    exit;
}

// Fetch cart items from the database
$cartItems = [];
$totalProductAmount = 0;
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $sql = "SELECT * FROM products WHERE product_id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_keys($_SESSION['cart']));
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cartItems as $item) {
        $totalProductAmount += $item['price'] * $_SESSION['cart'][$item['product_id']];
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Cart Container Styles */
        .cart-section {
            margin: 2rem auto;
            max-width: 1400px;
            padding: 0 1rem;
        }

        .cart-table-wrap {
            background: linear-gradient(145deg, #ffffff, #f0fffc);
            border-radius: 20px;
            border: 2px solid rgba(47, 153, 133, 0.2);
            box-shadow: 0 4px 20px rgba(47, 153, 133, 0.1);
            overflow: hidden;
        }

        /* Table Styles */
        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .cart-table-head {
            background: linear-gradient(145deg, #2F9985, #268571);
            color: #f8fafc;
        }

        .table-head-row th {
            padding: 1.2rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            color: #f8fafc;
        }

        .table-body-row {
            transition: all 0.3s ease;
        }

        .table-body-row:hover {
            background: rgba(47, 153, 133, 0.05);
        }

        .table-body-row td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(47, 153, 133, 0.1);
        }

        /* Product Image */
        .product-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(47, 153, 133, 0.1);
        }

        /* Remove Button */
        .product-remove a {
            color: #ff4444;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .product-remove a:hover {
            color: #cc0000;
            transform: scale(1.1);
        }

        /* Quantity Input */
        .quantity-input {
            width: 70px;
            padding: 0.5rem;
            border: 2px solid rgba(47, 153, 133, 0.2);
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .quantity-input:focus {
            outline: none;
            border-color: #2F9985;
            box-shadow: 0 0 0 2px rgba(47, 153, 133, 0.1);
        }

        /* Total Section */
        .total-section {
            background: linear-gradient(145deg, #ffffff, #f0fffc);
            border-radius: 20px;
            border: 2px solid rgba(47, 153, 133, 0.2);
            box-shadow: 0 4px 20px rgba(47, 153, 133, 0.1);
            padding: 2rem;
        }

        .total-table {
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .total-table-head {
            background: linear-gradient(145deg, #2F9985, #268571);
        }

        .total-table-head th {
            color: #f8fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .total-table th, .total-table td {
            padding: 1rem;
        }

        .total-data {
            border-bottom: 1px solid rgba(47, 153, 133, 0.1);
        }

        .total-data:last-child {
            border-bottom: none;
            font-size: 1.2rem;
            font-weight: bold;
            color: #2F9985;
        }

        /* Buttons */
        .cart-buttons {
            display: flex;
            gap: 1rem;
            flex-direction: column;
        }

        .boxed-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            background: linear-gradient(145deg, #2F9985, #268571);
            color: white;
            border: none;
        }

        .boxed-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(47, 153, 133, 0.2);
            background: linear-gradient(145deg, #268571, #1a5a4d);
            color: white;
        }

        .boxed-btn.black {
            background: linear-gradient(145deg, #333333, #222222);
        }

        .boxed-btn.black:hover {
            background: linear-gradient(145deg, #222222, #111111);
        }

        /* Empty Cart Message */
        .empty-cart {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        @media (max-width: 992px) {
            .cart-buttons {
                flex-direction: column;
            }
            
            .cart-table-wrap {
                overflow-x: auto;
            }
        }

        /* Update existing styles */
        .cart-table-head {
            background: linear-gradient(145deg, #2F9985, #268571);
            color: #f8fafc;
        }

        .table-head-row th {
            padding: 1.2rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            color: #f8fafc;
        }

        /* Add/Update header text styles */
        .breadcrumb-text p {
            color: #94a3b8;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .breadcrumb-text h1 {
            color: #0f172a;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .total-table-head {
            background: linear-gradient(145deg, #2F9985, #268571);
        }

        .total-table-head th {
            color: #f8fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .product-name {
            color: #1e293b;
            font-weight: 500;
        }

        .product-price, .product-total {
            color: #334155;
            font-weight: 500;
        }

        .total-data td strong {
            color: #0f172a;
        }

        .total-data:last-child td {
            color: #2F9985;
            font-weight: 700;
        }

        /* Update empty cart message colors */
        .empty-cart p {
            color: #475569;
            font-size: 1.1rem;
            margin: 1rem 0;
        }

        /* Table Headers Styling */
        .cart-table-head {
            background: linear-gradient(145deg, #2F9985, #268571);
        }

        .table-head-row th {
            padding: 1.2rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            color: #0f172a; /* Tailwind slate-900 */
        }

        /* Total Table Headers */
        .total-table-head th {
            color: #0f172a; /* Tailwind slate-900 */
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        /* Product Table Headers */
        .cart-table th {
            color: #0f172a; /* Tailwind slate-900 */
            font-weight: 600;
            background: linear-gradient(145deg, #f8fafc, #f1f5f9); /* Lighter gradient background */
            border-bottom: 2px solid rgba(47, 153, 133, 0.2);
        }

        /* Total Section Headers */
        .total-section th {
            color: #0f172a; /* Tailwind slate-900 */
            font-weight: 600;
            background: linear-gradient(145deg, #f8fafc, #f1f5f9);
            padding: 1rem;
        }

        /* Add hover effect for better UX */
        .cart-table th:hover, 
        .total-table th:hover {
            background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
        }

        /* Ensure text contrast */
        .table-total-row th {
            color: #0f172a; /* Tailwind slate-900 */
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <?php include 'includes/_header.php'; ?>
    
    <!-- breadcrumb-section -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Your Shopping Cart</p>
                        <h1>Cart</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <!-- cart section -->
    <div class="cart-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-table-wrap">
                        <table class="cart-table">
                            <thead class="cart-table-head">
                                <tr class="table-head-row">
                                    <th class="product-remove"></th>
                                    <th class="product-image">Product Image</th>
                                    <th class="product-name">Name</th>
                                    <th class="product-price">Price</th>
                                    <th class="product-quantity">Quantity</th>
                                    <th class="product-total">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $totalAmount = 0; ?>
                                <?php if (!empty($cartItems)): ?>
                                    <?php foreach ($cartItems as $item): ?>
                                        <?php 
                                        $product_id = $item['product_id'];
                                        $quantity = $_SESSION['cart'][$product_id];
                                        $total = $item['price'] * $quantity;
                                        $totalAmount += $total;
                                        ?>
                                        <tr class="table-body-row">
                                            <td class="product-remove">
                                                <a href="cart.php?action=remove&id=<?= $product_id ?>" title="Remove item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                            <td class="product-image">
                                                <img src="<?= $item['image_url'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                            </td>
                                            <td class="product-name"><?= htmlspecialchars($item['name']) ?></td>
                                            <td class="product-price">$<?= number_format($item['price'], 2) ?></td>
                                            <td class="product-quantity">
                                                <input type="number" name="quantity" value="<?= intval($quantity) ?>" min="1" class="quantity-input" data-product-id="<?= intval($product_id) ?>" data-price="<?= number_format($item['price'], 2, '.', '') ?>">
                                            </td>
                                            <td class="product-total">$<?= number_format($total, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="empty-cart">
                                            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #2F9985; margin-bottom: 1rem;"></i>
                                            <p>Your cart is empty.</p>
                                            <a href="shop.php" class="boxed-btn" style="display: inline-flex; margin-top: 1rem;">
                                                <i class="fas fa-shopping-basket"></i>
                                                Start Shopping
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="total-section">
                        <table class="total-table">
                            <thead class="total-table-head">
                                <tr class="table-total-row">
                                    <th>Total</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="total-data">
                                    <td><strong>Subtotal: </strong></td>
                                    <td>$<?= number_format($totalAmount, 2) ?></td>
                                </tr>
                                <tr class="total-data">
                                    <td><strong>Shipping: </strong></td>
                                    <td>$45</td>
                                </tr>
                                <tr class="total-data">
                                    <td><strong>Total: </strong></td>
                                    <td>$<?= number_format($totalAmount + 45, 2) ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="cart-buttons">
                            <a href="shop.php" class="boxed-btn">
                                <i class="fas fa-shopping-basket"></i>
                                Continue Shopping
                            </a>
                            <a href="checkout.php" class="boxed-btn black">
                                <i class="fas fa-credit-card"></i>
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end cart section -->

    <?php include 'includes/_footer.php'; ?>
    <?php include 'includes/_register-login.php' ?>

    <!-- jquery -->
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <!-- bootstrap -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- isotope -->
    <script src="assets/js/jquery.isotope-3.0.6.min.js"></script>
    <!-- owl carousel -->
    <script src="assets/js/owl.carousel.min.js"></script>
    <!-- magnific popup -->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <!-- main js -->
    <script src="assets/js/main.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const subtotalElement = document.querySelector('.total-table .total-data:nth-child(1) td:last-child');
    const shippingElement = document.querySelector('.total-table .total-data:nth-child(2) td:last-child'); // Add this line
    const totalElement = document.querySelector('.total-table .total-data:last-child td:last-child');
    let shippingCost = 45; // Use let instead of const for modification

    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateCart(this);
        });
    });

    function updateCart(input) {
        const productId = input.dataset.productId;
        const price = parseFloat(input.dataset.price);
        const quantity = parseInt(input.value);
        const totalPriceElement = input.closest('tr').querySelector('.product-total');
        
        if (isNaN(price) || isNaN(quantity)) {
            console.error('Invalid price or quantity');
            return;
        }
        
        const newTotal = price * quantity;

        totalPriceElement.textContent = '$' + newTotal.toFixed(2);

        updateTotals();
        updateServerCart(productId, quantity);
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.product-total').forEach(el => {
            const value = parseFloat(el.textContent.replace('$', '').trim());
            if (!isNaN(value)) {
                subtotal += value;
            }
        });

        // Set shipping cost to 0 if subtotal is more than 500
        shippingCost = subtotal > 500 ? 0 : 45;

        // Update the shipping element text
        shippingElement.textContent = '$' + shippingCost.toFixed(2);

        const total = subtotal + shippingCost;

        subtotalElement.textContent = '$' + subtotal.toFixed(2);
        totalElement.textContent = '$' + total.toFixed(2);
    }

    function updateServerCart(productId, quantity) {
        fetch(`cart.php?action=update&id=${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `quantity=${quantity}`
        }).then(response => {
            if (!response.ok) {
                console.error('Failed to update cart on server');
            }
        });
    }
});
    </script>

</body>
</html>
