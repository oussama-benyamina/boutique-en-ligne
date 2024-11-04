<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

//function get_footer_html() {
    ob_start();
    include '../includes/_footer.php';
    $footer_html = ob_get_clean();
    return $footer_html;
//}

function send_notification_email($email, $first_name, $last_name) {
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
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for registering!';
        
       // $footer_html = get_footer_html();
        
        // Email body with logo and footer
        $body = "
        <html>
        <body>
            <img src='#' alt='Ynextwach' style='max-width: 200px;'>
            <h2>Welcome to Ynextwach, {$first_name}!</h2>
            <p>Thank you for using our service! We appreciate your business.</p>
            <p>Best Regards,<br>Team Laplateforme GRP7</p>
            <hr>
            
        </body>
        </html>
        ";
        
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
?>
