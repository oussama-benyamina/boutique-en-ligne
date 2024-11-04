<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../functions/db_conn.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../notify_subscribers.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if (!isset($_SESSION['client_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins' && $_SESSION['user_role'] !== 'support')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    // Validate input
    if (empty($_POST['name']) || empty($_POST['description']) || empty($_POST['price'])) {
        throw new Exception('Required fields are missing');
    }

    // Handle file upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/img/products/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $upload_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            throw new Exception('Failed to upload image');
        }
        $image_url = 'assets/img/products/' . $file_name;
    }

    $pdo->beginTransaction();

    // Insert product into database
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

    $newProductId = $pdo->lastInsertId();
    
    // Fetch the newly added product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$newProductId]);
    $newProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();

    // Notify subscribers about the new product
    notifySubscribers($newProduct['name'], $newProduct['description'], $newProduct['price'], $newProduct['image_url']);

    echo json_encode(['success' => true, 'message' => 'Product added successfully and notifications sent']);

} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log('Add Product Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
