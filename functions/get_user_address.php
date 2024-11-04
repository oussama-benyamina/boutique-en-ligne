<?php
session_start();
require 'db_conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$client_id = $_SESSION['client_id'];

try {
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone_number, address, city, postal_code, country FROM clients WHERE client_id = ?");
    $stmt->execute([$client_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['success' => true, 'address' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
