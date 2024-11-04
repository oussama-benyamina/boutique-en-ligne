<?php
session_start();
require 'functions/db_conn.php';
require 'functions/wishlist_functions.php';

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$user_connected = isset($_SESSION['client_id']);

// If not logged in, redirect to login
if (!$user_connected) {
    header('Location: index.php');
    exit;
}

// Fetch wishlist items
$stmt = $pdo->prepare("
    SELECT p.* 
    FROM products p 
    JOIN wishlists w ON p.product_id = w.product_id 
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$_SESSION['client_id']]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <link rel="stylesheet" href="assets/css/shop.css">
    <title>My Wishlist</title>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <!-- breadcrumb-section -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>View your saved items</p>
                        <h1>My Wishlist</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <div class="product-section mt-150 mb-150">
        <div class="container">
            <div class="row product-lists">
                <?php if (count($wishlist_items) > 0): ?>
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="col-lg-4 col-md-6 text-center">
                            <div class="single-product-item">
                                <div class="product-image">
                                    <a href="single-product.php?id=<?= $item['product_id'] ?>">
                                        <img src="<?= $item['image_url'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </a>
                                    <button class="remove-from-wishlist" data-product-id="<?= $item['product_id'] ?>" title="Remove from wishlist">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="product-price"><span>Price</span> $<?= number_format($item['price'], 2) ?></p>
                                <form action="cart.php?action=add&id=<?= $item['product_id'] ?>" method="POST">
                                    <input type="number" name="quantity" value="1" min="1" class="product-quantity-input">
                                    <button type="submit" class="cart-btn"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="empty-wishlist-message">
                            <i class="fas fa-heart" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                            <h3>Your wishlist is empty</h3>
                            <p>Browse our products and add your favorites to the wishlist!</p>
                            <a href="shop.php" class="cart-btn" style="margin-top: 20px;">Continue Shopping</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>

    <!-- Add this before closing </body> tag -->
    <script>
    document.querySelectorAll('.remove-from-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
                return;
            }
            
            const productId = this.dataset.productId;
            const productCard = this.closest('.col-lg-4');
            
            // Add removing animation class
            productCard.querySelector('.single-product-item').classList.add('removing');
            
            fetch('functions/handle_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'removed') {
                    setTimeout(() => {
                        productCard.remove();
                        
                        // Check if wishlist is empty
                        if (document.querySelectorAll('.single-product-item').length === 0) {
                            // Show empty wishlist message
                            const productLists = document.querySelector('.product-lists');
                            productLists.innerHTML = `
                                <div class="col-12 text-center">
                                    <div class="empty-wishlist-message">
                                        <i class="fas fa-heart" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                                        <h3>Your wishlist is empty</h3>
                                        <p>Browse our products and add your favorites to the wishlist!</p>
                                        <a href="shop.php" class="cart-btn" style="margin-top: 20px;">Continue Shopping</a>
                                    </div>
                                </div>
                            `;
                        }
                    }, 300); // Match the CSS transition duration
                } else {
                    alert('Error removing item from wishlist. Please try again.');
                    productCard.querySelector('.single-product-item').classList.remove('removing');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                productCard.querySelector('.single-product-item').classList.remove('removing');
            });
        });
    });
    </script>
</body>
</html> 