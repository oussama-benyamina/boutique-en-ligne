<?php
// logout.php
session_start();
require 'db_conn.php';

if (isset($_SESSION['session_id'])) {
    $stmt = $pdo->prepare("UPDATE user_sessions SET logout_time = NOW() WHERE session_id = ?");
    $stmt->execute([$_SESSION['session_id']]);
}

session_destroy();
header("Location: ../index.php");
exit();
?>