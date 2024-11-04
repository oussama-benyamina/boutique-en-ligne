<?php
require '../functions/db_conn.php';
session_start();

if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin') {
    exit('Unauthorized');
}

$start_date = $_GET['startDate'] ?? date('Y-m-d', strtotime('last monday'));
$end_date = $_GET['endDate'] ?? date('Y-m-d', strtotime('next sunday'));

$sql_signatures = "SELECT c.first_name, c.last_name, COUNT(us.signature_time) as signature_count
                   FROM clients c
                   LEFT JOIN user_sessions us ON c.client_id = us.user_id
                   WHERE c.role = 'support' 
                   AND us.signature_time BETWEEN ? AND ?
                   GROUP BY c.client_id";

$stmt_signatures = $pdo->prepare($sql_signatures);
$stmt_signatures->execute([$start_date, $end_date]);
$signature_data = $stmt_signatures->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($signature_data);