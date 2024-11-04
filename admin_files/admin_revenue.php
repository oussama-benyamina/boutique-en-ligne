<?php
require '../functions/db_conn.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {
    header("Location: ../index.php");
    exit();
}

// Initialize filter variables
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// SQL query to get the total revenue
$sql = "SELECT SUM(amount) as total_revenue 
        FROM payments 
        WHERE payment_date BETWEEN :start_date AND :end_date";

$stmt = $pdo->prepare($sql);
$stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$total_revenue = $result['total_revenue'] ?? 0;

// SQL query to get payment details
$sql_details = "SELECT p.payment_id, p.order_id, p.payment_date, p.amount, p.payment_method, c.email
                FROM payments p
                JOIN orders o ON p.order_id = o.order_id
                JOIN clients c ON o.client_id = c.client_id
                WHERE p.payment_date BETWEEN :start_date AND :end_date
                ORDER BY p.payment_date DESC";

$stmt_details = $pdo->prepare($sql_details);
$stmt_details->execute(['start_date' => $start_date, 'end_date' => $end_date]);
$payment_details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/_nav.php'; ?>

    <div class="container mt-4">
        <h1>Revenue</h1>

        <form class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col">
                    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <h2>Total: <?php echo number_format($total_revenue, 2); ?> €</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Customer Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payment_details as $payment): ?>
                <tr>
                    <td><?php echo $payment['payment_id']; ?></td>
                    <td><?php echo $payment['order_id']; ?></td>
                    <td><?php echo $payment['payment_date']; ?></td>
                    <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                    <td><?php echo $payment['payment_method']; ?></td>
                    <td><?php echo $payment['email']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>