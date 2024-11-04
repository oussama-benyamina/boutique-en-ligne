<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'functions/db_conn.php';

function notifySubscribers($productName, $productDescription, $productPrice, $productImage) {
    global $pdo;

    // Fetch all subscribers
    $stmt = $pdo->query("SELECT email FROM subscribers");
    $subscribers = $stmt->fetchAll(PDO::FETCH_COLUMN);

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

        // Sender
        $mail->setFrom('ynextwach@gmail.com', 'Y nextwatch');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Product Alert: ' . $productName;
        $mail->Body    = "
            <h2>Check out our latest product!</h2>
            <h3>{$productName}</h3>
            <p>{$productDescription}</p>
            <p>Price: \${$productPrice}</p>
            <img src='{$productImage}' alt='{$productName}' style='max-width: 300px;'>
            <p><a href='http://yourwebsite.com/shop.php'>Shop Now</a></p>
        ";

        // Send to each subscriber
        foreach ($subscribers as $email) {
            $mail->addAddress($email);
            $mail->send();
            $mail->clearAddresses();
        }

        return true;
    } catch (Exception $e) {
        error_log("Failed to send notification emails: " . $mail->ErrorInfo);
        return false;
    }
}

