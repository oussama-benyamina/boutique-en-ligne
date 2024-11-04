<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['client_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins' && $_SESSION['user_role'] !== 'support')) {
    die('Unauthorized access');
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get user orders
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE client_id = ? ORDER BY order_date DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '<div class="card">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '</h5>
                    <p><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>
                    <p><strong>Role:</strong> ' . htmlspecialchars($user['role']) . '</p>
                    <p><strong>Phone:</strong> ' . htmlspecialchars($user['phone_number']) . '</p>
                    <p><strong>Address:</strong> ' . htmlspecialchars($user['address']) . '</p>
                    <p><strong>City:</strong> ' . htmlspecialchars($user['city']) . '</p>
                    <p><strong>Postal Code:</strong> ' . htmlspecialchars($user['postal_code']) . '</p>
                    <p><strong>Country:</strong> ' . htmlspecialchars($user['country']) . '</p>
                </div>
            </div>';
    
    $orders_html = '';
    if (!empty($orders)) {
        $orders_html = '<h5 class="mt-4">Order History</h5>
                       <table class="table table-striped">
                           <thead>
                               <tr>
                                   <th>Order ID</th>
                                   <th>Date</th>
                                   <th>Status</th>
                                   <th>Total</th>
                               </tr>
                           </thead>
                           <tbody>';
        
        foreach ($orders as $order) {
            $orders_html .= '<tr>
                               <td>' . htmlspecialchars($order['order_id']) . '</td>
                               <td>' . htmlspecialchars($order['order_date']) . '</td>
                               <td>' . htmlspecialchars($order['status']) . '</td>
                               <td>' . htmlspecialchars($order['total_amount']) . ' €</td>
                           </tr>';
        }
        
        $orders_html .= '</tbody></table>';
    } else {
        $orders_html = '<p class="mt-4">No orders found for this user.</p>';
    }
    
    echo json_encode(['details' => $html, 'orders' => $orders_html]);
} 