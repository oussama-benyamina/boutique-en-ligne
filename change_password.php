<?php
require 'functions/db_conn.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    } else {
        // Fetch the current password hash
        $sql = "SELECT password FROM clients WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $update_sql = "UPDATE clients SET password = ? WHERE email = ?";
            $update_stmt = $pdo->prepare($update_sql);
            if ($update_stmt->execute([$new_password_hash, $user_email])) {
                $message = "Password successfully updated.";
            } else {
                $message = "An error occurred. Please try again.";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <?php include 'includes/_head-index.php'; ?>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Fresh and Organic</p>
                        <h1>Change Password</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="change-password-container mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <h2>Change Your Password</h2>
                    <?php if ($message): ?>
                        <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
                        </div>
                        <button type="submit" class="boxed-btn">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>

  
</body>
</html>