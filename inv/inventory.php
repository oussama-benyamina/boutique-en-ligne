<?php
// inventory.php
require '../functions/db_conn.php';

session_start();



// Check if the user is logged in and has the correct role
if (!isset($_SESSION['client_id']) || ($_SESSION['user_role'] !== 'admin'  && $_SESSION['user_role'] !== 'admins' && $_SESSION['user_role'] !== 'support')) {
    header("Location: ../index.php");
    exit();
}

// Set the number of products per page
$productsPerPage = 10;

// Get the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $productsPerPage;

// Get the total number of products
$totalProductsStmt = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $totalProductsStmt->fetchColumn();

// Calculate the total number of pages
$totalPages = ceil($totalProducts / $productsPerPage);

// Check for highlighted product
$highlightProductId = isset($_GET['highlight']) ? (int)$_GET['highlight'] : null;

// Fetch products for the current page, ordered by creation date (newest first)
if ($highlightProductId) {
    $sql = "SELECT * FROM products WHERE product_id = :highlight
            UNION
            SELECT * FROM products WHERE product_id != :highlight
            ORDER BY CASE WHEN product_id = :highlight THEN 0 ELSE 1 END, created_at DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':highlight', $highlightProductId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
} else {
    $sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/inventory.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
</head>
<body>


    <div class="container mt-4">
        <h1>Inventory Management</h1>

        <div class="mb-3">
            <button type="button" class="btn btn-primary" id="addProductBtn">
                <i class="fas fa-plus"></i> Add New Product
            </button>
            <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'admins'): ?>
                <a href="../admin_files/admin.php" class="btn btn-secondary">Back to Admin Dashboard</a>
            <?php else: ?>
                <a href="../index.php" class="btn btn-secondary">Back to Home</a>
            <?php endif; ?>

        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>" 
                            class="<?php echo ($highlightProductId == $product['product_id']) ? 'highlight-row' : ''; ?>">
                            <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                            <td class="product-img-cell">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="description-cell"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></td>
                            <td class="price-cell">$<?php echo htmlspecialchars($product['price']); ?></td>
                            <td><?php echo htmlspecialchars($product['brand']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td class="stock-cell"><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                            <td class="actions-cell">
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this product?');" 
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <nav class="pagination-container">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
$(document).ready(function() {
    // Open modal when Add New Product button is clicked
    $('#addProductBtn').click(function() {
        $('#addProductModal').modal('show');
    });

    // Handle image preview
    $('#image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(`<img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">`);
            }
            reader.readAsDataURL(file);
        }
    });

    // Handle form submission
    $('#submitProduct').click(function(e) {
        e.preventDefault();
        
        let formData = new FormData($('#addProductForm')[0]);
        
        $.ajax({
            url: 'add_product_ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitProduct').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Adding...');
            },
            success: function(response) {
                try {
                    if (response.success) {
                        $('#addProductModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to add product'
                        });
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Invalid server response'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to communicate with the server'
                });
            },
            complete: function() {
                $('#submitProduct').prop('disabled', false).html('Add Product');
            }
        });
    });

    // Search functionality
    $('#search-input').on('input', function() {
        var query = $(this).val();
        if (query.length >= 2) {
            $.ajax({
                url: 'search_products.php',
                method: 'GET',
                data: { query: query },
                success: function(data) {
                    $('#search-results').html(data);
                }
            });
        } else {
            $('#search-results').html('');
        }
    });
});
</script>

<!-- Add this modal HTML before the closing body tag -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock_quantity">Stock Quantity</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <input type="text" class="form-control" id="brand" name="brand" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control" id="category" name="category" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitProduct">Add Product</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
