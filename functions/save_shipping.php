<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$client_id = $_SESSION['client_id'];

// Retrieve shipping information from POST data
$name = $_POST['sp_name'] ?? '';
$email = $_POST['sp_email'] ?? '';
$address = $_POST['sp_address'] ?? '';
$phone = $_POST['sp_mobile'] ?? '';
$city = $_POST['city'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$country = $_POST['country'] ?? '';

// Validate input (you may want to add more thorough validation)
if (empty($name) || empty($email) || empty($address) || empty($phone) || empty($city) || empty($postal_code) || empty($country)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Save shipping information to a new table in the database
try {
    $sql = "INSERT INTO shipping_addresses (client_id, name, email, address, phone, city, postal_code, country, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$client_id, $name, $email, $address, $phone, $city, $postal_code, $country]);
    
    $shipping_id = $pdo->lastInsertId();

    // Save shipping information to session for use in the order process
    $_SESSION['shipping_info'] = [
        'shipping_id' => $shipping_id,
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'phone' => $phone,
        'city' => $city,
        'postal_code' => $postal_code,
        'country' => $country
    ];

    echo json_encode(['success' => true, 'message' => 'Shipping information saved']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
