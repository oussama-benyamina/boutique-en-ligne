<?php
// delete_product.php

// Include the database connection
require '../functions/db_conn.php';

// Start the session
session_start();



// Check if a product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: inventory.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch the product details to get the image filename
$stmt = $pdo->prepare("SELECT image_url FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: inventory.php');
    exit();
}

// Start a transaction
$pdo->beginTransaction();

try {
    // Delete related records in the inventory table
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE product_id = ?");
    $stmt->execute([$product_id]);

    // Delete related records in the product_reviews table
    $stmt = $pdo->prepare("DELETE FROM product_reviews WHERE product_id = ?");
    $stmt->execute([$product_id]);

    // Delete related records in the order_items table
    $stmt = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
    $stmt->execute([$product_id]);

    // Delete the product from the products table
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);

    // Commit the transaction
    $pdo->commit();

    // Delete the product image if it exists
    if ($product['image_url'] && file_exists($product['image_url'])) {
        unlink($product['image_url']);
    }

    // Redirect back to the inventory page with a success message
    header('Location: inventory.php?message=Product deleted successfully');
    exit();
} catch (Exception $e) {
    // If there's an error, roll back the transaction
    $pdo->rollBack();

    // Redirect back to the inventory page with an error message
    header('Location: inventory.php?error=Failed to delete product: ' . $e->getMessage());
    exit();
}