<?php
session_start();
require 'functions/db_conn.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'support') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Delete related records in order_items table
        $delete_items_sql = "DELETE FROM order_items WHERE order_id = ?";
        $delete_items_stmt = $pdo->prepare($delete_items_sql);
        $delete_items_stmt->execute([$order_id]);

        // Delete related records in payments table
        $delete_payments_sql = "DELETE FROM payments WHERE order_id = ?";
        $delete_payments_stmt = $pdo->prepare($delete_payments_sql);
        $delete_payments_stmt->execute([$order_id]);

        // Delete the order from orders table
        $delete_order_sql = "DELETE FROM orders WHERE order_id = ?";
        $delete_order_stmt = $pdo->prepare($delete_order_sql);
        $delete_order_stmt->execute([$order_id]);

        // Commit the transaction
        $pdo->commit();

        // Redirect back to order management page with success message
        header("Location: order_management.php?message=Order deleted successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $pdo->rollBack();
        // Redirect back to order management page with error message
        header("Location: order_management.php?error=Failed to delete order: " . $e->getMessage());
        exit();
    }
} else {
    // Redirect back to order management page if no order_id is provided
    header("Location: order_management.php?error=No order specified for deletion");
    exit();
}
