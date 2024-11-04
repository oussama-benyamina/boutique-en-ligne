<?php
// login.php
require 'db_conn.php';
session_start();

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = sanitize_input($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    try {
        $sql = "SELECT client_id, password, role, first_name, last_name FROM clients WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['client_id'] = $user['client_id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

            // Record login session
            $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, login_time) VALUES (?, NOW())");
            $stmt->execute([$user['client_id']]);
            $_SESSION['session_id'] = $pdo->lastInsertId();

            $redirect = '';
            if ($user['role'] === 'admin') {
                $redirect = '../admin_files/admin.php';
            } elseif ($user['role'] === 'support') {
                $redirect = '../admin_files/support_signature.php';
            } elseif ($user['role'] === 'admins') {
                $redirect = '../admin_files/admin.php';
            }else {
                $redirect = '../index.php';
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful', 
                'redirect' => $redirect
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred during login. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>