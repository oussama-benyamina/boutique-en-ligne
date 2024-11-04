<?php
require_once __DIR__ . '/vendor/autoload.php';
require 'functions/db_conn.php';
session_start();

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if the token is valid and not expired
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password === $confirm_password) {
                // Update the password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE clients SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE client_id = ?");
                $stmt->execute([$hashed_password, $user['client_id']]);

                $message = "Your password has been successfully reset. You can now login with your new password.";
            } else {
                $message = "Passwords do not match. Please try again.";
            }
        }
    } else {
        $message = "Invalid or expired token. Please request a new password reset.";
    }
} else {
    $message = "Invalid request. Please use the reset link sent to your email.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php include 'includes/_head-index.php'; ?>

</head>
<body>

<div class="reset-password-container mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <h2>Reset Password</h2>
                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($user) && !$message): ?>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" id="new_password" name="new_password" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
                            </div>
                            <button type="submit" class="boxed-btn">Reset Password</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>

</body>

</html>