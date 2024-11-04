<?php
require 'db_conn.php';
session_start();

$message = '';
$user = null;

function format_first_name($first_name) {
    return ucfirst(strtolower($first_name));
}

function format_last_name($last_name) {
    return strtoupper($last_name);
}

function user_profile_complete($user) {
    return !empty($user['address']) && !empty($user['postal_code']) && 
           !empty($user['phone_number']) && !empty($user['city']) && !empty($user['country']);
}

// Vérifier si l'utilisateur arrive via le lien du mail (avec un token)
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifier le token et son expiration
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['temp_auth'] = true;
        $_SESSION['user_id'] = $user['client_id'];
        
        // Vérifier si le profil est complet
        if (user_profile_complete($user)) {
            header("Location: ../index.php");
            exit();
        }
    } else {
        $message = "Invalid or expired token.";
    }
} elseif (isset($_SESSION['temp_auth']) && $_SESSION['temp_auth'] === true) {
    // L'utilisateur a déjà été authentifié avec le token
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Vérifier si le profil est complet
    if (user_profile_complete($user)) {
        header("Location: ../index.php");
        exit();
    }
} else {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas authentifié
    header("Location: ../login.php");
    exit();
}

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_password = $_POST['new_password'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];
    $phone_number = $_POST['phone_number'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $first_name = format_first_name($_POST['first_name']);
    $last_name = format_last_name($_POST['last_name']);

    // Vérification et hachage du nouveau mot de passe
    if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $message = "The password must contain at least 8 characters, one uppercase letter, and one number.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Mise à jour des informations personnelles et suppression du token
        $stmt = $pdo->prepare("UPDATE clients SET password = ?, address = ?, postal_code = ?, phone_number = ?, city = ?, country = ?, first_name = ?, last_name = ?, reset_token = NULL, reset_token_expiry = NULL WHERE client_id = ?");
        if ($stmt->execute([$hashed_password, $address, $postal_code, $phone_number, $city, $country, $first_name, $last_name, $_SESSION['user_id']])) {
            $message = "Profile updated successfully!";
            // Rediriger vers la page d'index après la mise à jour
            header("Location: ../index.php");
            exit();
        } else {
            $message = "Error updating profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - Y nextwatch</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 50px;
        }
        h2 {
            color: #007bff;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Complete Your Profile</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="new_password">Set Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
                <small class="form-text text-muted">Password must contain at least 8 characters, one uppercase letter, and one number.</small>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>" required>
            </div>
            <input type="hidden" name="update_profile" value="1">
            <button type="submit" class="btn btn-primary btn-block">Complete Profile</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>