<?php
require '../functions/db_conn.php';
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['client_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'support')) {
    exit('Unauthorized access');
}

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    
    $sql = "SELECT product_id, name, category FROM products WHERE name LIKE ? OR category LIKE ? LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$query, $query]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        foreach ($results as $result) {
            echo '<a href="#" class="list-group-item list-group-item-action search-result" data-product-id="' . $result['product_id'] . '">';
            echo htmlspecialchars($result['name']) . ' - ' . htmlspecialchars($result['category']);
            echo '</a>';
        }
    } else {
        echo '<div class="list-group-item">No results found</div>';
    }
}
