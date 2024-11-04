<?php
require 'functions/db_conn.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Fetch current user data
$sql = "SELECT * FROM clients WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $country = $_POST['country'] ?? '';

    $update_sql = "UPDATE clients SET 
                   first_name = ?, last_name = ?, phone_number = ?, 
                   address = ?, city = ?, postal_code = ?, country = ? 
                   WHERE email = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $result = $update_stmt->execute([
        $first_name, $last_name, $phone_number, 
        $address, $city, $postal_code, $country, 
        $user_email
    ]);

    if ($result) {
        $message = "Profile updated successfully!";
        // Refresh user data
        $stmt->execute([$user_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
                        <h1>Edit Profile</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="edit-profile-container mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h2>Edit Your Profile</h2>
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <form action="edit_profile.php" method="post">
                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number:</label>
                            <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="city">City:</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Postal Code:</label>
                            <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code']); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="country">Country:</label>
                            <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" class="form-control">
                        </div>
                        <button type="submit" class="boxed-btn">Update Profile</button>
                    </form>
                    <div class="mt-3">
                        <a href="profile.php" class="boxed-btn">Back to Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>

    <
</body>
</html>