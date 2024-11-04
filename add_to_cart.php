<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    // Add product to cart logic here
    // Update $_SESSION['cart']
    
    // Fetch updated cart data
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN (" . implode(',', array_fill(0, count($_SESSION['cart']), '?')) . ")");
    $stmt->execute(array_keys($_SESSION['cart']));
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total = 0;
    foreach ($cartItems as &$item) {
        $item['quantity'] = $_SESSION['cart'][$item['id']];
        $total += $item['price'] * $item['quantity'];
    }
    
    echo json_encode([
        'items' => $cartItems,
        'total' => number_format($total, 2)
    ]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}