<?php
session_start();
require '../functions/db_conn.php';

if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'support') {
    echo 'error';
    exit();
}

$user_id = $_SESSION['client_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signature_data = $_POST['signature'];
    
    // Convert image data to binary
    $signature_binary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signature_data));
    
    // Save signature and set has_signed flag in session
    $stmt = $pdo->prepare("UPDATE user_sessions SET signature_image = ?, signature_time = NOW() WHERE session_id = ? AND user_id = ?");
    $result = $stmt->execute([$signature_binary, $_SESSION['session_id'], $user_id]);
    
    if ($result) {
        $_SESSION['has_signed'] = true; // Add this flag
        echo 'success';
    } else {
        echo 'error';
    }
    exit();
}

echo 'error';
