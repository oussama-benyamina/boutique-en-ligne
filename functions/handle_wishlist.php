<?php
session_start();
require 'db_conn.php';
require 'wishlist_functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['client_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (isset($_POST['product_id'])) {
    $result = toggleWishlist($_POST['product_id'], $_SESSION['client_id']);
    echo json_encode($result);
} else {
    echo json_encode(['error' => 'No product specified']);
} 