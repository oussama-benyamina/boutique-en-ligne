<?php
session_start();
require 'functions/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['client_id'])) {
    $product_id = $_POST['product_id'];
    $client_id = $_SESSION['client_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $photo_url = null;

    // Gérer le téléchargement de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $newname = uniqid() . '.' . $filetype;
            $upload_dir = 'uploads/review_photos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $newname)) {
                $photo_url = $upload_dir . $newname;
            }
        }
    }

    $sql = "INSERT INTO comments_ratings (product_id, client_id, rating, comment, photo_url) 
            VALUES (:product_id, :client_id, :rating, :comment, :photo_url)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':product_id' => $product_id,
        ':client_id' => $client_id,
        ':rating' => $rating,
        ':comment' => $comment,
        ':photo_url' => $photo_url
    ]);

    header("Location: single-product.php?id=" . $product_id);
    exit();
} else {
    header("Location: login.php");
    exit();
}
