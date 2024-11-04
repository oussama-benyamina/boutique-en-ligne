<?php
// edit_product.php

// Include the database connection
require '../functions/db_conn.php';

// Start the session
session_start();

if (!isset($_SESSION['client_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'support')) {

    header("Location: ../index.php");
    exit();
}

// Check if a product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: inventory.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch the product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]); 
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: inventory.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $brand = $_POST['brand'];
    $category = $_POST['category'];
    $stock_quantity = $_POST['stock_quantity'];

    // Update the product in the database
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, brand = ?, category = ?, stock_quantity = ? WHERE product_id = ?");
    $stmt->execute([$name, $description, $price, $brand, $category, $stock_quantity, $product_id]);

    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/img/products/';
        $file_name = $product_id . '_' . basename($_FILES['image']['name']);
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_url = $upload_path;
            $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE product_id = ?");
            $stmt->execute([$image_url, $product_id]);
        }
    }

    header('Location: inventory.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/inventory.css">
    <style>
        .edit-product-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            padding: 0.75rem;
        }

        .current-image-container {
            margin: 1rem 0;
        }

        .current-image {
            max-width: 200px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            margin-left: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="inventory-header">
            <div class="header-content">
                <h1><i class="fas fa-edit"></i> Edit Product</h1>
                <div class="action-buttons">
                    <a href="inventory.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>
            </div>
        </div>

        <div class="edit-product-container">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" class="form-control" id="price" name="price" 
                           step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="brand">Brand:</label>
                    <input type="text" class="form-control" id="brand" name="brand" 
                           value="<?php echo htmlspecialchars($product['brand']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <input type="text" class="form-control" id="category" name="category" 
                           value="<?php echo htmlspecialchars($product['category']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity:</label>
                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                           value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="image">Product Image:</label>
                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                    
                    <?php if (!empty($product['image_url'])): ?>
                        <div class="current-image-container">
                            <p>Current Image:</p>
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 alt="Current Product Image" class="current-image">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="inventory.php" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>