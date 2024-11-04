<?php
require '../functions/db_conn.php';
session_start();

if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {
    header("Location: ../index.php");
    exit();
}

?>