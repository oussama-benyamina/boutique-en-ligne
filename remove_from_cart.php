<?php
session_start(); // Start the session

// Check if the cart is set in the session
if (isset($_SESSION['cart'])) {
    // Get the product ID from the URL
    if (isset($_GET['id'])) {
        $productId = intval($_GET['id']); // Ensure it's an integer

        // Remove the product from the cart
        if (($key = array_search($productId, $_SESSION['cart'])) !== false) {
            unset($_SESSION['cart'][$key]);
        }
    }
}

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>
