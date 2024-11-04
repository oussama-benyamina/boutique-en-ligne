<?php
session_start();
require 'functions/db_conn.php';
require 'functions/wishlist_functions.php';

$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? $_GET['max_price'] : '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($selected_category) {
    $sql .= " AND category = :category";
    $params['category'] = ucfirst($selected_category);
}

if ($search) {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
    $params['search'] = "%$search%";
}

if ($min_price !== '') {
    $sql .= " AND price >= :min_price";
    $params['min_price'] = $min_price;
}

if ($max_price !== '') {
    $sql .= " AND price <= :max_price";
    $params['max_price'] = $max_price;
}

// Pagination setup
$items_per_page = 21;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Modify the SQL query to include LIMIT and OFFSET with named parameters
$sql .= " LIMIT :limit OFFSET :offset";

// Prepare the statement
$stmt = $pdo->prepare($sql);

// Bind the parameters for the main query
foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value, 
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
    );
}
$stmt->bindValue(":limit", $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

// Execute the statement
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total products for pagination
$count_sql = "SELECT COUNT(*) FROM products WHERE 1=1";
$count_params = [];

if ($selected_category) {
    $count_sql .= " AND category = :category";
    $count_params['category'] = ucfirst($selected_category);
}
if ($search) {
    $count_sql .= " AND (name LIKE :search OR description LIKE :search)";
    $count_params['search'] = "%$search%";
}
if ($min_price !== '') {
    $count_sql .= " AND price >= :min_price";
    $count_params['min_price'] = $min_price;
}
if ($max_price !== '') {
    $count_sql .= " AND price <= :max_price";
    $count_params['max_price'] = $max_price;
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($count_params);
$total_products = $count_stmt->fetchColumn();

$total_pages = ceil($total_products / $items_per_page);

// Fetch categories
$categories = [];
$category_sql = "SELECT * FROM categories";
$category_stmt = $pdo->prepare($category_sql);
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="assets/css/shop.css">
    <?php include 'includes/_head-index.php'; ?>
    <style>
        .wishlist-btn {
            border: none;
            background: none;
            padding: 5px;
        }

        .wishlist-btn:hover > i {
            color: #2F9985;
        }

        .wishlist-btn:hover {
            color:#247a6b;
        }

        .cart-btn {
            background: #2F9985;
            border: 1px solid #2F9985;
            padding: 8px;
            color: white;
            border-radius: 25px;
        }

        .cart-btn:hover {
            background: black;
            transition: 1s;
        }

        .filter-form {
            margin: 30px 0;
            padding: 20px;
            background-color: #f0f8ff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .filter-form .form-group {
            margin-bottom: 15px;
        }
        .filter-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .filter-form input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }
        .filter-btn {
            background-color: #2F9985;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .filter-btn:hover {
            background-color: #247a6b;
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
                        <p>Browse our collection</p>
                        <h1>Shop</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <!-- products -->
    <div class="product-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-filters">
                        <ul>
                            <li class="<?php echo !$selected_category ? 'active' : ''; ?>">
                                <a href="shop.php?<?= http_build_query(array_filter(['search' => $search, 'min_price' => $min_price, 'max_price' => $max_price])) ?>">All Products</a>
                            </li>
                            <?php foreach ($categories as $category): ?>
                                <li class="<?php echo $selected_category == strtolower($category['name']) ? 'active' : ''; ?>">
                                    <a href="shop.php?<?= http_build_query(array_filter(['category' => strtolower($category['name']), 'search' => $search, 'min_price' => $min_price, 'max_price' => $max_price])) ?>"><?= htmlspecialchars($category['name']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <form action="shop.php" method="GET" class="filter-form">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="min_price">Minimum Price:</label>
                                    <input type="number" name="min_price" id="min_price" placeholder="Min $" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_price">Maximum Price:</label>
                                    <input type="number" name="max_price" id="max_price" placeholder="Max $" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="filter-btn">Apply Filters</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($selected_category) ?>">
                    </form>
                </div>
            </div>

            <div class="row product-lists">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $item): ?>
                        <div class="col-lg-4 col-md-6 text-center <?= strtolower($item['category']); ?>">
                            <div class="single-product-item">
                                <div class="product-image">
                                    <a href="single-product.php?id=<?= $item['product_id'] ?>">
                                        <img src="<?= $item['image_url'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </a>
                                    <?php if ($user_connected): ?>
                                        <button class="wishlist-btn <?= isInWishlist($item['product_id'], $_SESSION['client_id']) ? 'in-wishlist' : '' ?>" 
                                                data-product-id="<?= $item['product_id'] ?>">
                                            <i class="fas fa-heart"></i> <span>Add to Wishlist</span>
                                        </button>
                                    <?php endif; ?>
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
                    <div class="col-lg-12">
                        <p class="text-center">No products available.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="pagination-wrap">
                        <ul>
                            <?php if ($page > 1): ?>
                                <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Prev</a></li>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <li><a class="<?= $i == $page ? 'active' : '' ?>" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a></li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end products -->

    <?php include 'includes/_footer.php'; ?>
    <?php include 'includes/_register-login.php' ?>

    <!-- Add this before closing </body> tag -->
    <script>
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const button = this;
            
            fetch('functions/handle_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'added') {
                    button.classList.add('in-wishlist');
                    // Optional: Show success message
                    alert('Product added to wishlist!');
                } else if (data.status === 'removed') {
                    button.classList.remove('in-wishlist');
                    // Optional: Show removal message
                    alert('Product removed from wishlist!');
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
    </script>

</body>

</html>
