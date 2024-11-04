<?php
session_start();
require 'functions/db_conn.php'; // Include the database connection

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : 0;

// Create directory for profile pictures if it doesn't exist
$profile_pictures_dir = './assets/img/uploads/profile_pictures/';
if (!file_exists($profile_pictures_dir)) {
    mkdir($profile_pictures_dir, 0755, true);
}

// Set default profile picture path
$default_profile_picture = 'assets/img_perso/default_profile.png';

// Fetch user details
$sql = "SELECT * FROM clients WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Function to get the correct profile picture path
function getProfilePicturePath($user, $profile_pictures_dir, $default_profile_picture) {
    if (!empty($user['profile_picture']) && file_exists($profile_pictures_dir . $user['profile_picture'])) {
        return $profile_pictures_dir . $user['profile_picture'];
    }
    return $default_profile_picture;
}

// Handle profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "./assets/img/uploads/profile_pictures/";
    $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
    $new_file_name = $user['client_id'] . '.' . $file_extension;
    $target_file = $target_dir . $new_file_name;
    $uploadOk = 1;

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo json_encode(['success' => false, 'message' => 'File is not an image.']);
        exit;
    }

    // Check file size (limit to 5MB)
    if ($_FILES["profile_picture"]["size"] > 5000000) {
        echo json_encode(['success' => false, 'message' => 'Sorry, your file is too large.']);
        exit;
    }

    // Allow certain file formats
    if ($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif") {
        echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
        exit;
    }

    // If everything is ok, try to upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update the user's profile picture in the database
            $sql = "UPDATE clients SET profile_picture = ? WHERE client_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$new_file_name, $user['client_id']]);
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
            $stmt->execute([$user['client_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'message' => 'Profile picture updated successfully.', 'new_image' => $target_file]);
            exit;
        } else {
            $error = error_get_last();
            echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file. Error: ' . $error['message']]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed. Please check the file and try again.']);
        exit;
    }
}

// Fetch user's orders
$sql_orders = "SELECT o.*, p.payment_method, p.status AS payment_status 
               FROM orders o 
               LEFT JOIN payments p ON o.order_id = p.order_id 
               WHERE o.client_id = ? 
               ORDER BY o.order_date DESC";
$stmt_orders = $pdo->prepare($sql_orders);
$stmt_orders->execute([$user['client_id']]);
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's reviews
$sql_reviews = "SELECT pr.*, p.name AS product_name 
                FROM comments_ratings pr 
                JOIN products p ON pr.product_id = p.product_id 
                WHERE pr.client_id = ? 
                ORDER BY pr.created_at DESC";
$stmt_reviews = $pdo->prepare($sql_reviews);
$stmt_reviews->execute([$user['client_id']]);
$reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

// Fetch user profile details
$prenom = $user['first_name'];
$nom = $user['last_name'];
$email = $user['email'];
$numero_telephone = $user['phone_number'];
$adresse = $user['address'];
$ville = $user['city'];
$code_postal = $user['postal_code'];
$pays = $user['country'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/css/profile.css">
    <?php include 'includes/_head-index.php'; ?>
    <title>User Profile</title>
    
    <style>
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 15px 20px;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            transform: translateX(100%);
            max-width: 300px;
        }
        .custom-alert.show {
            opacity: 1;
            transform: translateX(0);
        }
        .custom-alert.success {
            background-color: #267a6d;
        }
        .custom-alert.error {
            background-color: #ff6b6b;
        }

        table.cart-table > tbody > tr.table-body-row > td {text-align: center !important;}
    </style>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Your Profile</p>
                        <h1>User Profile</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-container mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="profile-sidebar">
                        <h3>My Account</h3>
                        <ul>
                            <li><a href="#overview">General Overview</a></li>
                            <li><a href="#orders">Orders</a></li>
                            <li><a href="edit_profile.php">Settings</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <section id="overview">
                        <h2>General Overview</h2>
                        <div class="profile-info">
                            <img src="<?php echo getProfilePicturePath($user, $profile_pictures_dir, $default_profile_picture); ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
                            <form action="" method="post" enctype="multipart/form-data" id="profilePictureForm">
                                <div class="file-input-wrapper">
                                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display: none;">
                                    <label for="profile_picture" class="boxed-btn">Upload Picture</label>
                                </div>
                            </form>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($numero_telephone); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($adresse . ', ' . $ville . ', ' . $code_postal . ', ' . $pays); ?></p>
                        </div>
                    </section>
                    <section id="orders">
                        <h2>Orders</h2>
                        <?php if (empty($orders)): ?>
                            <p>You haven't placed any orders yet.</p>
                        <?php else: ?>
                            <table class="cart-table">
                                <thead class="cart-table-head">
                                    <tr class="table-head-row">
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Payment Method</th>
                                        <th>Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="table-body-row">
                                            <td><?php echo $order['order_id']; ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo $order['status']; ?></td>
                                            <td>€<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><?php echo $order['payment_method'] ?? 'N/A'; ?></td>
                                            <td><?php echo $order['payment_status'] ?? 'N/A'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div id="customAlert" class="custom-alert"></div>

    <?php include 'includes/_footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function showCustomAlert(message, type) {
        var alertElement = $('#customAlert');
        alertElement.text(message);
        alertElement.removeClass('success error show').addClass(type);
        setTimeout(function() {
            alertElement.addClass('show');
        }, 10);
        
        setTimeout(function() {
            alertElement.removeClass('show');
        }, 3000);
    }

    $(document).ready(function() {
        $('#profile_picture').change(function() {
            var formData = new FormData($('#profilePictureForm')[0]);
            
            $.ajax({
                url: 'profile.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#profilePicture').attr('src', response.new_image + '?t=' + new Date().getTime());
                        showCustomAlert(response.message, 'success');
                    } else {
                        showCustomAlert(response.message, 'error');
                    }
                },
                error: function() {
                    showCustomAlert('An error occurred while uploading the image.', 'error');
                }
            });
        });
    });
    </script>

</body>
</html>