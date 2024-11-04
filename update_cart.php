<?php
session_start();

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        // Update the cart
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        // Remove the item if quantity is zero
        unset($_SESSION['cart'][$product_id]);
    }

    // Calculate total amount
    require 'functions/db_conn.php';
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

    // Return updated total amounts as JSON
    echo json_encode([
        'subtotal' => number_format($totalAmount, 2),
        'shipping' => number_format($shippingCost, 2),
        'total' => number_format($finalTotal, 2)
    ]);
}
