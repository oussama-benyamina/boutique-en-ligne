<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function send_reset_email($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ynextwach@gmail.com';
        $mail->Password   = 'gbwxmdkeapzbjyoh';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('ynextwach@gmail.com', 'Y nextwatch');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $reset_link = "http://localhost/boutiaue_plateforme/reset_password.php?token=" . $token;
        $mail->Body    = "Click the following link to reset your password: <a href='$reset_link'>$reset_link</a>";
        $mail->AltBody = "Click the following link to reset your password: $reset_link";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send reset email: " . $mail->ErrorInfo);
        return false;
    }
}

function send_welcome_email($email, $first_name, $role, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ynextwach@gmail.com';
        $mail->Password   = 'gbwxmdkeapzbjyoh';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('ynextwach@gmail.com', 'Y nextwatch');
        $mail->addAddress($email, $first_name);

        // Update profile link
        $update_profile_link = "http://localhost/boutiaue_plateforme/functions/update_profile.php?token=" . $token;

        // Motivation message for admin and support
        $motivation_message = '';
        if ($role == 'admin') {
            $motivation_message = "As an administrator, you play a crucial role in our team. Your leadership and decision-making skills will be instrumental in driving our success. We're excited to have you on board!";
        } elseif ($role == 'support') {
            $motivation_message = "Welcome to our support team! Your dedication to helping our customers will be key to our success. We're thrilled to have you join us in providing excellent service.";
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Y nextwatch';
        $mail->Body    = "
            <h1>Welcome to Y nextwatch!</h1>
            <p>Hello {$first_name},</p>
            <p>Your account has been created successfully.</p>
            <p><strong>Role:</strong> {$role}</p>
            " . ($motivation_message ? "<p><strong>Special Message:</strong> {$motivation_message}</p>" : "") . "
            <p><strong>Important:</strong> Please click on the following link to complete your profile:</p>
            <p><a href='{$update_profile_link}'>{$update_profile_link}</a></p>
            <p><strong>Note:</strong> This link will expire in 8 hours.</p>
            <p>You will need to provide the following information:</p>
            <ul>
                <li>Password</li>
                <li>Address</li>
                <li>Postal Code</li>
                <li>Phone Number</li>
                <li>City</li>
                <li>Country</li>
            </ul>
            <p>If you have any questions, please don't hesitate to contact us.</p>
            <p>Best regards,<br>The Y nextwatch Team</p>
        ";
        $mail->AltBody = "
            Welcome to Y nextwatch!
            
            Hello {$first_name},
            
            Your account has been created successfully.
            
            Role: {$role}
            " . ($motivation_message ? "\nSpecial Message: {$motivation_message}\n" : "") . "
            Important: Please visit the following link to complete your profile:
            {$update_profile_link}
            
            Note: This link will expire in 8 hours.
            
            You will need to provide the following information:
            - Password
            - Address
            - Postal Code
            - Phone Number
            - City
            - Country
            
            If you have any questions, please don't hesitate to contact us.
            
            Best regards,
            The Y nextwatch Team
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send welcome email: " . $mail->ErrorInfo);
        return false;
    }
}

function sendThankYouEmail($clientEmail, $orderDetails) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ynextwach@gmail.com';
        $mail->Password   = 'gbwxmdkeapzbjyoh'; // Make sure this is an app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Enable SMTP debug mode
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP DEBUG: $str");
        };

        // Recipients
        $mail->setFrom('ynextwach@gmail.com', 'Y nextwatch');
        $mail->addAddress($clientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Thank You for Your Order from Y nextwatch';

        // Email body
        $body = "
        <html>
        <body>
            <h2>Thank you for your purchase from Y nextwatch!</h2>
            <p>Here are your order details:</p>
            <table>
                <tr>
                    <th>Order ID:</th>
                    <td>{$orderDetails['order_id']}</td>
                </tr>
                <tr>
                    <th>Total Amount:</th>
                    <td>$" . number_format($orderDetails['total_amount'], 2) . "</td>
                </tr>
            </table>
            <h3>Products:</h3>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>";

        foreach ($orderDetails['items'] as $item) {
            $body .= "
                <tr>
                    <td>{$item['name']}</td>
                    <td>{$item['quantity']}</td>
                    <td>$" . number_format($item['unit_price'], 2) . "</td>
                </tr>";
        }

        $body .= "
            </table>
            <p>You can view your order details by clicking the link below:</p>
            <a href='http://localhost/boutiaue_plateforme/my-orders.php?order_id={$orderDetails['order_id']}'>View Order Details</a>
            <p>Thank you again for your purchase!</p>
            <p>Best Regards,<br>The Y nextwatch Team</p>
        </body>
        </html>";

        $mail->Body = $body;

        error_log("Attempting to send thank you email to: $clientEmail");
        error_log("PHPMailer configuration for thank you email:");
        error_log("Host: " . $mail->Host);
        error_log("Port: " . $mail->Port);
        error_log("SMTPSecure: " . $mail->SMTPSecure);
        error_log("SMTPAuth: " . ($mail->SMTPAuth ? 'true' : 'false'));
        error_log("Username: " . $mail->Username);
        error_log("From: " . $mail->From);
        error_log("FromName: " . $mail->FromName);
        error_log("Subject: " . $mail->Subject);
        error_log("To: " . $clientEmail);

        $result = $mail->send();
        error_log("PHPMailer send() result: " . ($result ? 'true' : 'false'));
        
        if ($result) {
            error_log("Thank you email sent successfully to: $clientEmail");
            return true;
        } else {
            throw new Exception("Mailer Error: " . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        error_log("Failed to send thank you email to $clientEmail. Error: " . $e->getMessage());
        return false;
    }
}
?>
