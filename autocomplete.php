<?php
// Connexion à la base de données
require 'functions/db_conn.php';

if (isset($_GET['query'])) {
    $searchTerm = $_GET['query'] . '%';
    $fetchImages = isset($_GET['fetchImages']) && $_GET['fetchImages'] === 'true';

    // Modify product query based on whether we need images
    $product_sql = "SELECT product_id, name" . 
                   ($fetchImages ? ", image_url" : "") . 
                   " FROM products WHERE name LIKE :searchTerm LIMIT 5";
    $product_stmt = $pdo->prepare($product_sql);
    $product_stmt->execute([':searchTerm' => $searchTerm]);
    $products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Category query remains the same
    $category_sql = "SELECT category_id, name FROM categories WHERE name LIKE :searchTerm LIMIT 5";
    $category_stmt = $pdo->prepare($category_sql);
    $category_stmt->execute([':searchTerm' => $searchTerm]);
    $categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

    $suggestions = [];

    foreach ($products as $product) {
        $suggestion = [
            'type' => 'product',
            'id' => $product['product_id'],
            'name' => $product['name']
        ];
        
        if ($fetchImages && isset($product['image_url'])) {
            $suggestion['image_url'] = $product['image_url'];
        }
        
        $suggestions[] = $suggestion;
    }

    foreach ($categories as $category) {
        $suggestions[] = [
            'type' => 'category',
            'id' => $category['category_id'],
            'name' => $category['name']
        ];
    }

    // Only return suggestions if there are any
    if (empty($suggestions)) {
        echo json_encode([]);
    } else {
        echo json_encode($suggestions);
    }
}
