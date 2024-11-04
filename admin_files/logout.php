<?php
require '../functions/db_conn.php';
session_start();

if (isset($_SESSION['client_id'])) {
    // Update user_sessions table
    $stmt = $pdo->prepare("UPDATE user_sessions 
                          SET logout_time = NOW() 
                          WHERE user_id = ? 
                          AND logout_time IS NULL");
    $stmt->execute([$_SESSION['client_id']]);
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}

// Redirect to index page
header("Location: ../index.php");
exit();
