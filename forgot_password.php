
<?php
require_once __DIR__ . '/vendor/autoload.php';
require 'functions/db_conn.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        
        // Store the token in the database
        $stmt = $pdo->prepare("UPDATE clients SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->execute([$token, $email]);

        // Send reset password email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ynextwach@gmail.com';
            $mail->Password   = 'gbwxmdkeapzbjyoh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('ynextwach@gmail.com', 'Y nextwatch');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $reset_link = "http://localhost/boutiaue_plateforme/reset_password.php?token=" . $token;
            $mail->Body    = "Click the following link to reset your password: <a href='$reset_link'>$reset_link</a>";

            $mail->send();
            $message = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "Failed to send reset email. Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include 'includes/_head-index.php'; ?>

</head>
<body>

<div class="forgot-password-container mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <h2>Forgot Password</h2>
                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required class="form-control">
                        </div>
                        <button type="submit" class="boxed-btn">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>

</body>

</html>