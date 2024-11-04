<?php
require '../functions/db_conn.php';
session_start();

if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {
    header("Location: ../functions/login.php");
    exit();
}
if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {
    header("Location: ../index.php");
    exit();
}



$message = '';

if (isset($_GET['id'])) {
    $client_id = (int)$_GET['id'];
    $sql = "SELECT * FROM clients WHERE client_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$client_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        echo "User not found!";
        exit();
    }

    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($user);
        exit;
    }
} else {
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }
    echo "Invalid request!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ['success' => false, 'message' => '', 'user' => null];
    
    try {
        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $email = htmlspecialchars(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
        $role = htmlspecialchars($_POST['role']);
        $phone_number = htmlspecialchars($_POST['phone_number']);
        $address = htmlspecialchars($_POST['address']);
        $city = htmlspecialchars($_POST['city']);
        $postal_code = htmlspecialchars($_POST['postal_code']);
        $country = htmlspecialchars($_POST['country']);
        
        $sql = "UPDATE clients SET first_name = ?, last_name = ?, email = ?, role = ?, phone_number = ?, address = ?, city = ?, postal_code = ?, country = ? WHERE client_id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$first_name, $last_name, $email, $role, $phone_number, $address, $city, $postal_code, $country, $client_id])) {
            // Fetch updated user data
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
            $stmt->execute([$client_id]);
            $updated_user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $response['success'] = true;
            $response['message'] = "User updated successfully!";
            $response['user'] = $updated_user;
        } else {
            $response['message'] = "Error updating user.";
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Edit User: <span id="user-name"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></span></h1>
        <div id="message" class="alert" style="display: none;"></div>
        <form id="editUserForm" method="POST" action="">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Role:</label>
                <select class="form-control" name="role" id="role">
                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="support" <?php echo $user['role'] == 'support' ? 'selected' : ''; ?>>Support</option>
                    <?php if ($_SESSION['user_role'] === 'admins' ): ?>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" class="form-control" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" name="address" id="address" value="<?php echo htmlspecialchars($user['address']); ?>">
            </div>
            
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" class="form-control" name="city" id="city" value="<?php echo htmlspecialchars($user['city']); ?>">
            </div>
            
            <div class="form-group">
                <label for="postal_code">Postal Code:</label>
                <input type="text" class="form-control" name="postal_code" id="postal_code" value="<?php echo htmlspecialchars($user['postal_code']); ?>">
            </div>
            
            <div class="form-group">
                <label for="country">Country:</label>
                <input type="text" class="form-control" name="country" id="country" value="<?php echo htmlspecialchars($user['country']); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
        <a href="manage.php" class="btn btn-secondary mt-3">Back to Admin Manage</a>
    </div> 

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'edit_user.php?id=<?php echo $client_id; ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    $('#message').text(response.message).removeClass('alert-danger').addClass('alert-success').show();
                    $('#user-name').text(response.user.first_name + ' ' + response.user.last_name);
                    updateFormFields(response.user);
                },
                error: function() {
                    $('#message').text('An error occurred while updating the user.').removeClass('alert-success').addClass('alert-danger').show();
                }
            });
        });
    });

    function updateFormFields(user) {
        $('#first_name').val(user.first_name);
        $('#last_name').val(user.last_name);
        $('#email').val(user.email);
        $('#role').val(user.role);
        $('#phone_number').val(user.phone_number);
        $('#address').val(user.address);
        $('#city').val(user.city);
        $('#postal_code').val(user.postal_code);
        $('#country').val(user.country);
    }

    function refreshUserData() {
        $.getJSON('edit_user.php?id=<?php echo $client_id; ?>&ajax=1', function(data) {
            $('#user-name').text(data.first_name + ' ' + data.last_name);
            updateFormFields(data);
        });
    }

    // Rafraîchir toutes les 2 secondes
    setInterval(refreshUserData, 2000);
    </script>
</body>
</html>