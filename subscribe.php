<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'functions/db_conn.php';  // Make sure this file contains your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subscriberEmail = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    if (filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL)) {
        // Save email to database
        $stmt = $pdo->prepare("INSERT IGNORE INTO subscribers (email) VALUES (:email)");
        $stmt->execute(['email' => $subscriberEmail]);

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ynextwach@gmail.com';
            $mail->Password   = 'gbwxmdkeapzbjyoh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('ynextwach@gmail.com', 'Y nextwatch');
            $mail->addAddress($subscriberEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Thanks for Subscribing!';
            $mail->Body    = 'Thank you for joining our family! We\'re excited to have you on board.';

            $mail->send();

            echo "<script>alert('Thank you for subscribing!'); window.location.href='index.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Subscription failed. Please try again.'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email address.'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
