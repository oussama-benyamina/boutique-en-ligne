<?php
session_start();
require 'functions/db_conn.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

// Fetch the product details from the database
if ($product_id > 0) {
    $sql = "SELECT * FROM products WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// If product not found, redirect to the shop page
if (!$product) {
    header("Location: shop.php");
    exit;
}

// Fetch average rating
$avg_rating_sql = "SELECT AVG(rating) as avg_rating FROM comments_ratings WHERE product_id = :product_id";
$avg_rating_stmt = $pdo->prepare($avg_rating_sql);
$avg_rating_stmt->execute([':product_id' => $product_id]);
$avg_rating = $avg_rating_stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'includes/_head-index.php'; ?>
    <style>
        .review-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .reviewer-profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .reviewer-details {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .reviewer-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .review-date {
            color: #666;
            font-size: 14px;
        }

        .review-content {
            margin-left: 65px;
        }

        .review-content p {
            margin-bottom: 15px;
        }

        .review-photo {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
        }

        .rating {
            display: flex;
            gap: 2px;
        }

        .rating i {
            color: #ffd700;
            font-size: 14px;
        }

        .rating i.fa-star-o {
            color: #ddd;
        }

        button.cart-btn {
            background: #2F9985;
            border: 1px solid #2F9985;
            padding: 8px;
            color: white;
            border-radius: 25px;
            margin: 15px;
        }

        button.cart-btn:hover {
            background-color: #051922;
            color: #ffb6b9;
            transform: scale(1.05);
        }

        .star-rating {
            display: inline-flex;
            flex-direction: row;
            gap: 5px;
        }

        .star-rating i {
            font-size: 32px;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .star-rating i.fas {
            color: #ffd700;
        }

        .star-rating i:hover,
        .star-rating i:hover ~ i {
            color: #ddd;
        }

        .star-rating i:hover {
            color: #ffd700;
        }

        .star-rating i:has(~ i:hover) {
            color: #ffd700;
        }

        .review-form-container {
            max-width: 600px;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .review-title {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .rating-group {
            margin-bottom: 20px;
        }

        .rating-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .star-rating {
            display: flex;
            flex-direction: row;
            gap: 5px;
        }

        .star-rating i {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .star-rating i.fas {
            color: #ffd700;
        }

        .star-rating i:hover,
        .star-rating i:hover ~ i {
            color: #ffd700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        textarea.form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        textarea.form-control:focus {
            outline: none;
            border-color: #2F9985;
            box-shadow: 0 0 0 2px rgba(74,144,226,0.2);
        }

        .photo-label {
            display: inline-block;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .photo-label:hover {
            background: #e9ecef;
        }

        .photo-label i {
            margin-right: 5px;
        }

        .form-control-file {
            display: none;
        }

        .submit-btn {
            background: #2F9985;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .submit-btn:hover {
            background: black;
        }

        .review-trigger {
            cursor: pointer;
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
        }

        .star-rating {
            display: inline-flex;
            flex-direction: row;
            gap: 5px;
        }

        .star-rating i {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .star-rating i.fas {
            color: #ffd700;
        }

        /* Fix for left-to-right hover effect */
        .star-rating i:hover ~ i {
            color: #ddd !important;
        }

        .star-rating:hover i {
            color: #ffd700;
        }

        /* Modal Styles */
        .review-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-step {
            text-align: center;
        }

        .rating-step h4,
        .comment-step h4 {
            margin-bottom: 20px;
            color: #333;
        }

        textarea.form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            resize: vertical;
            min-height: 120px;
        }

        .photo-upload {
            margin-bottom: 20px;
        }

        .photo-label {
            display: inline-block;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
        }

        .form-control-file {
            display: none;
        }

        .submit-btn {
            background: #2F9985;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .submit-btn:hover {
            background: black;
        }

        /* Add Review Button */
        .add-review-btn {
            background: #2F9985;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            font-size: 16px;
        }

        .add-review-btn:hover {
            background: black;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .add-review-btn i {
            color: #ffd700;
        }

        /* Modal Styles */
        .review-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.2s ease;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-step {
            text-align: center;
        }

        .rating-step h4,
        .comment-step h4 {
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
        }

        .star-rating {
            display: inline-flex;
            flex-direction: row;
            gap: 5px;
        }

        .star-rating i {
            font-size: 32px;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .star-rating i.fas {
            color: #ffd700;
        }

        /* Fix for left-to-right hover effect */
        .star-rating i:hover ~ i {
            color: #ddd !important;
        }

        .star-rating:hover i {
            color: #ffd700;
        }

        textarea.form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
            transition: border-color 0.2s ease;
        }

        textarea.form-control:focus {
            outline: none;
            border-color: #2F9985;
            box-shadow: 0 0 0 2px rgba(74,144,226,0.2);
        }

        .photo-upload {
            margin-bottom: 20px;
        }

        .photo-label {
            display: inline-block;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .photo-label:hover {
            background: #e9ecef;
        }

        .form-control-file {
            display: none;
        }

        .submit-btn {
            background: #2F9985;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .submit-btn:hover {
            background: black;
            transform: translateY(-1px);
        }

        .pagination-wrapper {
            margin: 30px 0;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #666;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: #f5f5f5;
        }

        .pagination a.active {
            background-color: #2F9985;
            color: white;
            border-color: #2F9985;
        }

        .pagination .prev,
        .pagination .next {
            background-color: #fff;
        }

        /* Add these media queries for mobile responsiveness */
        @media screen and (max-width: 768px) {
            .single-product .container .row {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .col-md-5, .col-md-7 {
                width: 100%;
                padding: 0 15px;
            }

            .single-product-img {
                margin-bottom: 30px;
            }

            .single-product-img img {
                max-width: 100%;
                height: auto;
                margin: 0 auto;
                display: block;
            }

            .single-product-content {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .rating {
                justify-content: center;
                margin: 15px 0;
            }

            .single-product-form {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
            }

            .single-product-form form {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                width: 100%;
                max-width: 300px;
            }

            .single-product-form input[type="number"] {
                
                text-align: center;
            }

            .product-share {
                justify-content: center;
                padding: 0;
            }
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
                        <p>See more Details</p>
                        <h1><?= htmlspecialchars($product['name']) ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <!-- single product -->
    <div class="single-product mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="single-product-img">
                        <img src="<?= $product['image_url'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="single-product-content">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="single-product-pricing"><span>Price</span> $<?= number_format($product['price'], 2) ?></p>
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= round($avg_rating) ? '' : '-o' ?>"></i>
                            <?php endfor; ?>
                            (<?= number_format($avg_rating, 1) ?>)
                        </div>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <div class="single-product-form">
                            <form action="cart.php?action=add&id=<?= $product['product_id'] ?>" method="POST">
                                <input type="number" name="quantity" placeholder="0" min="1" value="1">
                                <button type="submit" class="cart-btn"><i class="fas fa-shopping-cart"></i> Add </button>
                            </form>
                            <p><strong>Category: </strong><?= htmlspecialchars($product['category']) ?></p>
                        </div>
                        <h4>Share:</h4>
                        <ul class="product-share">
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end single product -->

    <!-- Reviews and Ratings Section -->
    <div class="product-reviews mt-100 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <h3>Customer Reviews</h3>

                    <!-- Display existing reviews -->
                    <?php
                    // Pagination settings
                    $reviews_per_page = 5;
                    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($current_page - 1) * $reviews_per_page;

                    // Get total number of reviews
                    $count_sql = "SELECT COUNT(*) FROM comments_ratings WHERE product_id = :product_id";
                    $count_stmt = $pdo->prepare($count_sql);
                    $count_stmt->execute([':product_id' => $product_id]);
                    $total_reviews = $count_stmt->fetchColumn();
                    $total_pages = ceil($total_reviews / $reviews_per_page);

                    // Modify review query to include pagination
                    $review_sql = "SELECT cr.*, c.first_name, c.last_name, c.profile_picture 
                                   FROM comments_ratings cr 
                                   JOIN clients c ON cr.client_id = c.client_id 
                                   WHERE cr.product_id = :product_id 
                                   ORDER BY cr.created_at DESC
                                   LIMIT :offset, :limit";
                    $review_stmt = $pdo->prepare($review_sql);
                    $review_stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
                    $review_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $review_stmt->bindValue(':limit', $reviews_per_page, PDO::PARAM_INT);
                    $review_stmt->execute();
                    $reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Add the function to get profile picture path (same as in profile.php)
                    function getProfilePicturePath($profile_picture) {
                        $profile_pictures_dir = './assets/img/uploads/profile_pictures/';
                        $default_profile_picture = 'assets/img_perso/default_profile.png';
                        
                        if (!empty($profile_picture) && file_exists($profile_pictures_dir . $profile_picture)) {
                            return $profile_pictures_dir . $profile_picture;
                        }
                        return $default_profile_picture;
                    }

                    foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <img src="<?= getProfilePicturePath($review['profile_picture']) ?>" 
                                         alt="Profile Picture" 
                                         class="reviewer-profile-pic">
                                    <div class="reviewer-details">
                                        <h4><?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?></h4>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <small class="review-date"><?= date('F d, Y', strtotime($review['created_at'])) ?></small>
                            </div>
                            <div class="review-content">
                                <p><?= htmlspecialchars($review['comment']) ?></p>
                                <?php if (!empty($review['photo_url'])): ?>
                                    <img src="<?= htmlspecialchars($review['photo_url']) ?>" alt="Review photo" class="review-photo">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Add pagination links after the reviews -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination-wrapper text-center mt-5">
                            <div class="pagination">
                                <?php if ($current_page > 1): ?>
                                    <a href="?id=<?= $product_id ?>&page=<?= $current_page - 1 ?>" class="prev">&laquo; Previous</a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?id=<?= $product_id ?>&page=<?= $i ?>" 
                                       class="<?= $i === $current_page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <a href="?id=<?= $product_id ?>&page=<?= $current_page + 1 ?>" class="next">Next &raquo;</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add review form -->
                    <?php if (isset($_SESSION['client_id'])): ?>
                        <!-- Review Trigger Button -->
                        <button class="add-review-btn" id="addReviewBtn" style="background-color:#2F9985;">
                            <i class="fas fa-star"></i> Add Review
                        </button>

                        <!-- Review Modal -->
                        <div class="review-modal" id="reviewModal">
                            <div class="modal-content">
                                <span class="close-modal">&times;</span>
                                <form action="add_review.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    <input type="hidden" name="rating" id="rating" value="" required>
                                    
                                    <div class="modal-step rating-step">
                                        <h4>Rate this product</h4>
                                        <div class="star-rating" id="modalStars">
                                            <i class="far fa-star" data-rating="1"></i>
                                            <i class="far fa-star" data-rating="2"></i>
                                            <i class="far fa-star" data-rating="3"></i>
                                            <i class="far fa-star" data-rating="4"></i>
                                            <i class="far fa-star" data-rating="5"></i>
                                        </div>
                                    </div>

                                    <div class="modal-step comment-step" style="display: none;">
                                        <h4>Write your review</h4>
                                        <textarea name="comment" id="comment" class="form-control" rows="4" required 
                                            placeholder="Share your experience with this product..."></textarea>
                                        <div class="photo-upload">
                                            <label for="photo" class="photo-label">
                                                <i class="fas fa-camera"></i> Add a photo (optional)
                                            </label>
                                            <input type="file" name="photo" id="photo" class="form-control-file" accept="image/*">
                                        </div>
                                        <button type="submit" class="submit-btn">Submit Review</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Please <a href="login.php">login</a> to leave a review.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- end Reviews and Ratings Section -->

    <!-- more products -->
    <div class="more-products mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="section-title">
                        <h3><span class="orange-text">Related</span> Products</h3>
                        <p>Explore our other products.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                // Fetch related products (you can modify the logic to select related products)
                $related_sql = "SELECT * FROM products WHERE product_id != :product_id LIMIT 3";
                $related_stmt = $pdo->prepare($related_sql);
                $related_stmt->execute([':product_id' => $product_id]);
                $related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($related_products as $related_product): ?>
                    <div class="col-lg-4 col-md-6 text-center">
                        <div class="single-product-item">
                            <div class="product-image">
                                <a href="single-product.php?id=<?= $related_product['product_id'] ?>"><img src="<?= $related_product['image_url'] ?>" alt="<?= htmlspecialchars($related_product['name']) ?>"></a>
                            </div>
                            <h3><?= htmlspecialchars($related_product['name']) ?></h3>
                            <p class="product-price">$<?= number_format($related_product['price'], 2) ?></p>
                            <a href="cart.php?action=add&id=<?= $related_product['product_id'] ?>" class="cart-btn"><i class="fas fa-shopping-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- end more products -->

    <?php include 'includes/_footer.php'; ?>
    <?php include 'includes/_register-login.php'; ?>

    <!-- jquery -->
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <!-- bootstrap -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- count down -->
    <script src="assets/js/jquery.countdown.js"></script>
    <!-- isotope -->
    <script src="assets/js/jquery.isotope-3.0.6.min.js"></script>
    <!-- waypoints -->
    <script src="assets/js/waypoints.js"></script>
    <!-- owl carousel -->
    <script src="assets/js/owl.carousel.min.js"></script>
    <!-- magnific popup -->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <!-- mean menu -->
    <script src="assets/js/jquery.meanmenu.min.js"></script>
    <!-- sticker js -->
    <script src="assets/js/sticker.js"></script>
    <!-- main js -->
    <script src="assets/js/main.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('reviewModal');
        const addReviewBtn = document.getElementById('addReviewBtn');
        const modalStars = document.getElementById('modalStars');
        const closeBtn = document.querySelector('.close-modal');
        const ratingInput = document.querySelector('input[name="rating"]');
        const ratingStep = document.querySelector('.rating-step');
        const commentStep = document.querySelector('.comment-step');

        // Function to update stars
        function updateStars(rating) {
            const stars = modalStars.querySelectorAll('i');
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                star.classList.remove('fas', 'far');
                star.classList.add(starRating <= rating ? 'fas' : 'far');
            });
        }

        // Open modal on button click
        addReviewBtn.addEventListener('click', function() {
            modal.style.display = 'block';
            ratingStep.style.display = 'block';
            commentStep.style.display = 'none';
            ratingInput.value = '';
            updateStars(0);
        });

        // Handle modal stars click and hover
        const stars = modalStars.querySelectorAll('i');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;
                updateStars(rating);
                
                // Show comment step
                ratingStep.style.display = 'none';
                commentStep.style.display = 'block';
            });

            star.addEventListener('mouseover', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach(s => {
                    const starRating = parseInt(s.getAttribute('data-rating'));
                    s.classList.remove('fas', 'far');
                    s.classList.add(starRating <= rating ? 'fas' : 'far');
                });
            });
        });

        modalStars.addEventListener('mouseleave', function() {
            updateStars(ratingInput.value || 0);
        });

        // Close modal
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>

</body>

</html>