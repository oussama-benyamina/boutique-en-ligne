<?php
require '../functions/db_conn.php';
session_start();

if (!isset($_SESSION['client_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'support')) {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle file upload
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/img/products/';
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'assets/img/products/' . $file_name;
            }
        }

        $sql = "INSERT INTO products (name, description, price, brand, category, stock_quantity, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['price'],
            $_POST['brand'],
            $_POST['category'],
            $_POST['stock_quantity'],
            $image_url
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
