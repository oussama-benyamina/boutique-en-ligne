<?php
session_start();
require '../functions/db_conn.php';
require '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51Q7ZrvRqnrD4rjze7vQoTVAGJI8iMEvDdLSpx56pXqzptoiuqIRMKpaWVfaQMY6r7khJjZC5N0oC5ryNWgVXL7a700xUsYPRcP');

if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit;
}

$payment_intent_id = $_GET['payment_intent'] ?? null;

if (!$payment_intent_id) {
    header('Location: ../cart.php?error=invalid_payment');
    exit;
}

try {
    $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

    if ($payment_intent->status === 'succeeded') {
        // Payment successful, process the order
        $order_id = processOrder($pdo, $payment_intent);
        
        // Clear the cart and shipping info
        unset($_SESSION['cart']);
        unset($_SESSION['shipping_info']);
        
        // Redirect to a thank you page
        header("Location: ../thank-you.php?order_id=$order_id");
        exit;
    } else {
        // Payment failed or is still pending
        header('Location: ../cart.php?error=payment_incomplete');
        exit;
    }
} catch (Exception $e) {
    error_log("Stripe error: " . $e->getMessage());
    header('Location: ../cart.php?error=payment_error');
    exit;
}

function processOrder($pdo, $payment_intent) {
    // Start transaction
    $pdo->beginTransaction();

    try {
        $shipping_info = $_SESSION['shipping_info'];
        $client_id = $_SESSION['client_id'];

        // Insert into orders table
        $order_sql = "INSERT INTO orders (client_id, order_date, payment_status, status, total_amount, shipping_address_id) VALUES (?, NOW(), ?, ?, ?, ?)";
        $order_stmt = $pdo->prepare($order_sql);
        $order_stmt->execute([
            $client_id,
            'Paid', // Set payment_status to 'Paid'
            'Pending', // Set delivery status to 'Pending'
            $payment_intent->amount / 100, // Convert cents to dollars
            $shipping_info['shipping_id']
        ]);
        $order_id = $pdo->lastInsertId();

        // Insert order items
        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
        $item_stmt = $pdo->prepare($item_sql);

        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product_sql = "SELECT price FROM products WHERE product_id = ?";
            $product_stmt = $pdo->prepare($product_sql);
            $product_stmt->execute([$product_id]);
            $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

            $item_stmt->execute([
                $order_id,
                $product_id,
                $quantity,
                $product['price']
            ]);
        }

        // Insert payment record
        $payment_sql = "INSERT INTO payments (order_id, payment_date, amount, payment_method, status) VALUES (?, NOW(), ?, 'Stripe', 'Completed')";
        $payment_stmt = $pdo->prepare($payment_sql);
        $payment_stmt->execute([
            $order_id,
            $payment_intent->amount / 100
        ]);

        // Commit transaction
        $pdo->commit();

        return $order_id;
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Error processing order: " . $e->getMessage());
        throw $e;
    }
}
