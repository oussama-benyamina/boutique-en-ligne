<?php
function isInWishlist($product_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetchColumn() > 0;
}

function toggleWishlist($product_id, $user_id) {
    global $pdo;
    
    try {
        if (isInWishlist($product_id, $user_id)) {
            $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            return ['status' => 'removed'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $product_id]);
            return ['status' => 'added'];
        }
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
} 