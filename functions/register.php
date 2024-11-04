<?php
session_start();
require 'db_conn.php';

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function format_first_name($first_name) {
    return ucfirst(strtolower($first_name));
}

function format_last_name($last_name) {
    return strtoupper($last_name);
}

$response = ['success' => false, 'messages' => []];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = isset($_POST['first_name']) ? format_first_name(sanitize_input($_POST['first_name'])) : '';
    $nom = isset($_POST['last_name']) ? format_last_name(sanitize_input($_POST['last_name'])) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $mot_de_passe = isset($_POST['password']) ? $_POST['password'] : '';
    $repeat_password = isset($_POST['repeat_password']) ? $_POST['repeat_password'] : '';
    $numero_telephone = isset($_POST['phone_number']) ? sanitize_input($_POST['phone_number']) : '';
    $adresse = isset($_POST['address']) ? sanitize_input($_POST['address']) : '';
    $ville = isset($_POST['city']) ? sanitize_input($_POST['city']) : '';
    $code_postal = isset($_POST['postal_code']) ? sanitize_input($_POST['postal_code']) : '';
    $pays = isset($_POST['country']) ? sanitize_input($_POST['country']) : '';

    if (strlen($mot_de_passe) < 8 || !preg_match('/[A-Z]/', $mot_de_passe) || !preg_match('/[0-9]/', $mot_de_passe)) {
        $response['messages'][] = "The password must contain at least 8 characters, one uppercase letter, and one number.";
    }

    if ($mot_de_passe !== $repeat_password) {
        $response['messages'][] = "The passwords do not match.";
    }

    if (empty($response['messages'])) {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $sql = "INSERT INTO clients (first_name, last_name, email, password, phone_number, address, city, postal_code, country, created_at, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'user')";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$prenom, $nom, $email, $hashed_password, $numero_telephone, $adresse, $ville, $code_postal, $pays]);
            
            $client_id = $pdo->lastInsertId();
            
            $_SESSION['client_id'] = $client_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $prenom . ' ' . $nom;
            $_SESSION['user_role'] = 'user';

            $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, login_time) VALUES (?, NOW())");
            $stmt->execute([$client_id]);
            $_SESSION['session_id'] = $pdo->lastInsertId();

            $response['success'] = true;
            $response['messages'][] = "Registration successful!";
            
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $response['messages'][] = "This email has already been used.";
            } else {
                $response['messages'][] = "Error: " . $e->getMessage();
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
