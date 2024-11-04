<?php 
require 'functions/db_conn.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <!-- search area -->
    <div class="search-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <span class="close-btn"><i class="fas fa-window-close"></i></span>
                    <div class="search-bar">
                        <div class="search-bar-tablecell">
                            <h3>Search For:</h3>
                            <input type="text" placeholder="Keywords">
                            <button type="submit">Search <i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end search area -->

    <!-- breadcrumb-section -->
    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Get 24/7 Support</p>
                        <h1>Contact us</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end breadcrumb section -->

    <!-- contact form -->
    <div class="contact-from-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-5 mb-lg-0">
                    <div class="form-title">
                        <h2>QUESTIONS? our team is here to help you</h2>
                        <p style="font-weight: bold; font-size: 1.2em; color: #333; background-color: #f8f8f8; padding: 10px; border-left: 4px solid #007bff;">We are here to help you with any questions you may have. Our team is dedicated to providing you with the best service and support.</p>
                    </div>
                    <?php
                    // Include PHPMailer
                    use PHPMailer\PHPMailer\PHPMailer;
                    use PHPMailer\PHPMailer\Exception;
                    require 'vendor/autoload.php'; // Adjust this path if necessary

                    $message_status = '';
                    
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $name = $_POST['name'];
                        $email = $_POST['email'];
                        $phone = $_POST['phone'];
                        $subject = $_POST['subject'];
                        $message = $_POST['message'];

                        if (!empty($name) && !empty($email) && !empty($message)) {
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

                                // Email content
                                $mail->isHTML(true);
                                $mail->Subject = $subject;
                                $mail->Body = "
                                    <h2>Contact Request</h2>
                                    <p><strong>Name:</strong> $name</p>
                                    <p><strong>Email:</strong> $email</p>
                                    <p><strong>Phone:</strong> $phone</p>
                                    <p><strong>Message:</strong><br>$message</p>
                                ";
                                $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message";

                                // Send the email
                                $mail->send();
                                $message_status = "<div class='alert alert-success'>Message sent successfully!</div>";
                            } catch (Exception $e) {
                                $message_status = "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
                            }
                        } else {
                            $message_status = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
                        }
                    }
                    ?>

                    <div id="form_status"><?php echo $message_status; ?></div>
                    <div class="contact-form">
                        <form method="POST" id="fruitkha-contact">
                            <p>
                                <input type="text" placeholder="Name" name="name" id="name" required>
                                <input type="email" placeholder="Email" name="email" id="email" required>
                            </p>
                            <p>
                                <input type="tel" placeholder="Phone" name="phone" id="phone">
                                <input type="text" placeholder="Subject" name="subject" id="subject">
                            </p>
                            <p><textarea name="message" id="message" cols="30" rows="10" placeholder="Message" required></textarea></p>
                            <p><input type="submit" value="Submit"></p>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="contact-form-wrap">
                        <div class="contact-form-box">
                            <h4><i class="fas fa-map"></i> Shop Address</h4>
                            <p> Marseille<br> France</p>
                        </div>
                        <div class="contact-form-box">
                            <h4><i class="far fa-clock"></i> Shop Hours</h4>
                            <p>MON - FRIDAY: 8 to 9 PM <br> SAT - SUN: 10 to 8 PM </p>
                        </div>
                        <div class="contact-form-box">
                            <h4><i class="fas fa-address-book"></i> Contact</h4>
                            <p> Email: ynextwach@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end contact form -->


	<?php include 'includes/_footer.php'; ?>
    <?php include 'includes/_register-login.php'; ?>
	
	


    <!-- Include necessary scripts -->
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>