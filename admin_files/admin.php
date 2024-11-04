<?php
require '../functions/db_conn.php';
require '../functions/email_functions.php';
session_start();

if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {
    header("Location: ../index.php");
    exit();
}

// Common variables
$admin_name = htmlspecialchars($_SESSION['user_email']);
$message = '';
$roles = ['user', 'admin', 'support'];

// Function to delete user and related records
function deleteUser($pdo, $user_id) {
    $pdo->beginTransaction();
    try {
        // Get the user's email first
        $stmt = $pdo->prepare("SELECT email FROM clients WHERE client_id = ?");
        $stmt->execute([$user_id]);
        $user_email = $stmt->fetchColumn();

        // Delete user sessions
        $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Check if product_reviews table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'product_reviews'");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("DELETE FROM product_reviews WHERE client_id = ?");
            $stmt->execute([$user_id]);
        }
        
        // Delete shipping addresses
        $stmt = $pdo->prepare("DELETE FROM shipping_addresses WHERE client_id = ?");
        $stmt->execute([$user_id]);
        
        // Delete payments
        $stmt = $pdo->prepare("DELETE payments FROM payments 
                             INNER JOIN orders ON payments.order_id = orders.order_id 
                             WHERE orders.client_id = ?");
        $stmt->execute([$user_id]);
        
        // Delete order items
        $stmt = $pdo->prepare("DELETE order_items FROM order_items 
                             INNER JOIN orders ON order_items.order_id = orders.order_id 
                             WHERE orders.client_id = ?");
        $stmt->execute([$user_id]);
        
        // Delete orders
        $stmt = $pdo->prepare("DELETE FROM orders WHERE client_id = ?");
        $stmt->execute([$user_id]);

        // Delete comments and ratings
        $stmt = $pdo->prepare("DELETE FROM comments_ratings WHERE client_id = ?");
        $stmt->execute([$user_id]);
        
        // Delete from subscribers
        if ($user_email) {
            $stmt = $pdo->prepare("DELETE FROM subscribers WHERE email = ?");
            $stmt->execute([$user_email]);
        }
        
        // Finally delete the user
        $stmt = $pdo->prepare("DELETE FROM clients WHERE client_id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error deleting user: " . $e->getMessage());
        return false;
    }
}

// Handle user account creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    header('Content-Type: application/json');
    $response = array('success' => false, 'message' => '');
    
    try {
        $first_name = ucfirst(trim(strtolower($_POST['first_name'])));
        $last_name = trim(strtoupper($_POST['last_name']));
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        
        // Validation
        if (empty($first_name) || empty($last_name) || empty($email)) {
            throw new Exception("All fields are required.");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Email already exists.");
        }
        
        // Generate token for password reset
        $token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+8 hours'));
        
        // Begin transaction
        $pdo->beginTransaction();
        
        $sql_insert = "INSERT INTO clients (first_name, last_name, email, role, reset_token, reset_token_expiry, created_at, password) 
                      VALUES (?, ?, ?, ?, ?, ?, NOW(), NULL)";
        $stmt_insert = $pdo->prepare($sql_insert);
        
        if (!$stmt_insert->execute([$first_name, $last_name, $email, $role, $token, $token_expiry])) {
            throw new Exception("Error creating user account.");
        }
        
        // Send welcome email
        if (!send_welcome_email($email, $first_name, $role, $token)) {
            // Log the email error but don't roll back the transaction
            error_log("Failed to send welcome email to: $email");
            $response['message'] = "User account created, but failed to send welcome email.";
        } else {
            $response['message'] = "User account created successfully and welcome email sent.";
        }
        
        $pdo->commit();
        $response['success'] = true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id_to_delete = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($user_id_to_delete === false) {
        $error_message = "Invalid user ID.";
    } else if ($user_id_to_delete === $_SESSION['client_id']) {
        $error_message = "You cannot delete your own account.";
    } else if (deleteUser($pdo, $user_id_to_delete)) {
        $success_message = "User has been deleted successfully.";
    } else {
        $error_message = "There was an error deleting user.";
    }
}

// Manage Users Section - Fetch users
$selected_roles = isset($_GET['roles']) ? array_intersect($_GET['roles'], $roles) : $roles;
if (empty($selected_roles)) {
    $selected_roles = $roles;
}
$placeholders = implode(',', array_fill(0, count($selected_roles), '?'));
$sql_users = "SELECT client_id, first_name, last_name, email, role FROM clients WHERE role IN ($placeholders)";
$stmt_users = $pdo->prepare($sql_users);
$stmt_users->execute($selected_roles);
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Support Connection Section
$sql_supports = "SELECT client_id, CONCAT(first_name, ' ', last_name) AS full_name FROM clients WHERE role = 'support'";
$stmt_supports = $pdo->query($sql_supports);
$supports = $stmt_supports->fetchAll(PDO::FETCH_ASSOC);

$sql_active_signatures = "SELECT 
    c.client_id,
    c.first_name,
    c.last_name,
    c.email,
    us.login_time,
    us.logout_time,
    us.signature_time,
    COALESCE(us.signature_time, us.login_time) as last_activity,
    us.signature_image as current_signature
FROM clients c
JOIN user_sessions us ON c.client_id = us.user_id
WHERE c.role = 'support'
AND us.signature_image IS NOT NULL
AND DATE(us.signature_time) = :signature_date
ORDER BY us.signature_time DESC";

$signature_date = isset($_GET['signature_date']) ? $_GET['signature_date'] : date('Y-m-d');
$stmt_signatures = $pdo->prepare($sql_active_signatures);
$stmt_signatures->execute([':signature_date' => $signature_date]);
$active_signatures = $stmt_signatures->fetchAll(PDO::FETCH_ASSOC);

// Support signatures
$selected_support = isset($_GET['support']) ? filter_var($_GET['support'], FILTER_VALIDATE_INT) : null;
$selected_week = isset($_GET['week']) ? $_GET['week'] : date('Y-W');

if ($selected_support) {
    list($year, $week) = explode('-', $selected_week);
    $start_date = date('Y-m-d', strtotime($year . "W" . $week . "1"));
    $end_date = date('Y-m-d', strtotime($year . "W" . $week . "7"));

    $sql_signatures = "SELECT us.signature_time, us.signature_image
                      FROM user_sessions us
                      WHERE us.user_id = :user_id
                      AND us.signature_time BETWEEN :start_date AND :end_date
                      ORDER BY us.signature_time DESC";

    $stmt_signatures = $pdo->prepare($sql_signatures);
    $stmt_signatures->execute([
        ':user_id' => $selected_support,
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ]);
    $signatures = $stmt_signatures->fetchAll(PDO::FETCH_ASSOC);
}

// Stats Section
$available_roles = ['admin', 'support', 'user'];
$selected_roles_stats = isset($_GET['roles_stats']) ? array_intersect($_GET['roles_stats'], $available_roles) : $available_roles;
if (empty($selected_roles_stats)) {
    $selected_roles_stats = $available_roles;
}
$start_date_stats = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('monday this week'));
$end_date_stats = date('Y-m-d', strtotime($start_date_stats . ' +6 days'));

// Stats users query
$sql_users_stats = "SELECT client_id, CONCAT(first_name, ' ', last_name) as full_name, role 
                    FROM clients 
                    WHERE role IN (" . implode(',', array_fill(0, count($selected_roles_stats), '?')) . ")
                    ORDER BY role, last_name";
$stmt_users_stats = $pdo->prepare($sql_users_stats);
$stmt_users_stats->execute($selected_roles_stats);
$users_stats = $stmt_users_stats->fetchAll(PDO::FETCH_ASSOC);

$selected_users = isset($_GET['users']) ? array_intersect($_GET['users'], array_column($users_stats, 'client_id')) : array_column($users_stats, 'client_id');
if (empty($selected_users)) {
    $selected_users = array_column($users_stats, 'client_id');
}

// Stats main query
$sql_stats = "SELECT 
                c.client_id,
                CONCAT(c.first_name, ' ', c.last_name) as full_name,
                c.role,
                DATE(us.login_time) as date,
                SUM(TIMESTAMPDIFF(MINUTE, us.login_time, IFNULL(us.logout_time, NOW()))) as total_minutes
            FROM 
                clients c
            LEFT JOIN
                user_sessions us ON c.client_id = us.user_id
            WHERE 
                c.role IN (" . implode(',', array_fill(0, count($selected_roles_stats), '?')) . ")
                AND c.client_id IN (" . implode(',', array_fill(0, count($selected_users), '?')) . ")
                AND (us.login_time IS NULL OR (DATE(us.login_time) BETWEEN ? AND ?))
            GROUP BY 
                c.client_id, DATE(us.login_time)
            ORDER BY 
                c.role, c.last_name, date";

$params_stats = array_merge($selected_roles_stats, $selected_users, [$start_date_stats, $end_date_stats]);
$stmt_stats = $pdo->prepare($sql_stats);
$stmt_stats->execute($params_stats);
$stats = $stmt_stats->fetchAll(PDO::FETCH_ASSOC);

// Prepare chart data
$dates = [];
$user_hours = [];
$total_hours = [];

try {
    // Generate dates array
    for ($i = 0; $i < 7; $i++) {
        $current_date = date('Y-m-d', strtotime($start_date_stats . ' +' . $i . ' days'));
        if ($current_date === false) {
            throw new Exception("Invalid date format");
        }
        $dates[] = date('d/m/Y', strtotime($current_date));
    }

    // Process stats data
    foreach ($stats as $row) {
        if (!isset($row['client_id']) || !isset($row['date']) || !isset($row['total_minutes'])) {
            continue;
        }

        $user_id = $row['client_id'];
        $date = $row['date'];
        $hours = round(floatval($row['total_minutes']) / 60, 2);
        
        if (!isset($user_hours[$user_id])) {
            $user_hours[$user_id] = [
                'name' => $row['full_name'] ?? '',
                'role' => $row['role'] ?? '',
                'hours' => array_fill(0, 7, 0)
            ];
        }
        
        if ($date !== null) {
            $formatted_date = date('d/m/Y', strtotime($date));
            $day_index = array_search($formatted_date, $dates);
            if ($day_index !== false) {
                $user_hours[$user_id]['hours'][$day_index] = $hours;
            }
        }
        
        if (!isset($total_hours[$user_id])) {
            $total_hours[$user_id] = 0;
        }
        $total_hours[$user_id] += $hours;
    }

    $grand_total = array_sum($total_hours);
    $grand_total_hours = floor($grand_total);
    $grand_total_minutes = round(($grand_total - $grand_total_hours) * 60);

    // Handle AJAX requests
    if(isset($_GET['ajax'])) {
        ob_start();
        include 'includes/users_table.php';
        $table_html = ob_get_clean();
        echo $table_html;
        exit;
    }

} catch (Exception $e) {
    error_log("Error in chart data preparation: " . $e->getMessage());
    $dates = [];
    $user_hours = [];
    $total_hours = [];
    $grand_total_hours = 0;
    $grand_total_minutes = 0;
}

// After session_start() and before the HTML
$stmt = $pdo->prepare("SELECT profile_picture, first_name, last_name FROM clients WHERE client_id = ?");
$stmt->execute([$_SESSION['client_id']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Update admin name to use full name
$admin_name = htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']);
$profile_picture = $user_data['profile_picture'];

// Profile picture path handling
$profile_pictures_dir = '../assets/img/uploads/profile_pictures/';
$default_profile_picture = '../assets/img/uploads/default.png';

// Get profile picture path using the same logic as profile.php
if (!empty($profile_picture) && file_exists($profile_pictures_dir . $profile_picture)) {
    $profile_path = $profile_pictures_dir . $profile_picture;
} else {
    $profile_path = $default_profile_picture;
}

// Dashboard statistics queries
$sql_total_users = "SELECT COUNT(*) FROM clients";
$total_users = $pdo->query($sql_total_users)->fetchColumn();

$sql_total_revenue = "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'Paid'";
$total_revenue = $pdo->query($sql_total_revenue)->fetchColumn();

$sql_total_subscribers = "SELECT COUNT(*) FROM subscribers";
$total_subscribers = $pdo->query($sql_total_subscribers)->fetchColumn();

$sql_total_products = "SELECT COUNT(*) FROM products";
$total_products = $pdo->query($sql_total_products)->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/admin.css">
    <style>
    .modal-header.bg-warning {
        background-color: #ffc107;
    }

    .modal-content {
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .form-control {
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    .btn-warning {
        color: #212529;
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }

    .alert {
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }
    </style>
</head>
<body>

<div class="admin-layout">
    <aside class="sidebar">
        <div class="profile-section">
            <img src="<?php echo htmlspecialchars($profile_path); ?>" 
                 alt="<?php echo htmlspecialchars($admin_name); ?>'s Profile" 
                 class="profile-image"
                 onerror="this.src='<?php echo htmlspecialchars($default_profile_picture); ?>'">
            <div class="admin-name"><?php echo $admin_name; ?></div>
            <div class="admin-role"><?php echo ucfirst($_SESSION['user_role']); ?></div>
        </div>

        <nav class="nav-menu">
            <a href="#dashboard" class="nav-link active">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            <a href="#users" class="nav-link">
                <i class="fas fa-users"></i>
                Manage Users
            </a>
           
            <a href="#statistics" class="nav-link">
                <i class="fas fa-chart-line"></i>
                Statistics
            </a>
            <a href="#support-signatures" class="nav-link">
                <i class="fas fa-signature"></i>
                Support Signatures
            </a>
            <a href="logout.php" class="nav-link" data-bypass="true">
    <i class="fas fa-sign-out-alt"></i>
    Logout
</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-container">
            <!-- Dashboard Section -->
            <div id="dashboard" class="section">
                <h2 class="section-title">Dashboard Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-users stat-icon"></i>
                        <h3>Total Users</h3>
                        <p class="stat-number"><?php echo $total_users; ?></p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-euro-sign stat-icon"></i>
                        <h3>Total Revenue</h3>
                        <p class="stat-number"><?php echo number_format($total_revenue, 2); ?>€</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-envelope stat-icon"></i>
                        <h3>Subscribers</h3>
                        <p class="stat-number"><?php echo $total_subscribers; ?></p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-box stat-icon"></i>
                        <h3>Total Products</h3>
                        <p class="stat-number"><?php echo $total_products; ?></p>
                    </div>
                </div>
            </div>

            <!-- Users Section -->
            <div id="users" class="section">
                <h2 class="section-title">User Management</h2>
                <div class="create-user-section">
                    <?php include 'includes/create_user_form.php'; ?>
                </div>
                <div class="manage-users-section">
                    <?php include 'includes/users_table.php'; ?>
                </div>
            </div>

            <!-- Statistics Section -->
            <div id="statistics" class="section">
                <h2 class="section-title">Statistics</h2>
                <div class="stats-filters mb-4">
                    <form method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label class="mr-2">Start Date:</label>
                            <input type="date" name="start_date" value="<?php echo $start_date_stats; ?>" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </form>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="chart-container">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-summary">
                            <h4>Summary</h4>
                            <p>Total Hours: <?php echo $grand_total_hours . 'h' . sprintf('%02d', $grand_total_minutes); ?></p>
                            <p>Active Users: <?php echo count($users_stats); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Signatures Section -->
            <div id="support-signatures" class="section">
                <h2 class="section-title">Support Signatures</h2>
                
                <div class="mb-4">
                    <form method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label class="mr-2">Filter by Date:</label>
                            <input type="date" name="signature_date" value="<?php echo isset($_GET['signature_date']) ? $_GET['signature_date'] : date('Y-m-d'); ?>" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>

                <?php if (empty($active_signatures)): ?>
                    <p class="alert alert-info">No support signatures found for the selected date.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Login Time</th>
                                    <th>Signature Time</th>
                                    <th>Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($active_signatures as $signature): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($signature['first_name'] . ' ' . $signature['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($signature['email']); ?></td>
                                        <td><?php echo date('d/m/Y H:i:s', strtotime($signature['login_time'])); ?></td>
                                        <td><?php echo date('d/m/Y H:i:s', strtotime($signature['signature_time'])); ?></td>
                                        <td>
                                            <?php if ($signature['current_signature']): ?>
                                                <img src="data:image/png;base64,<?php echo base64_encode($signature['current_signature']); ?>" 
                                                     alt="Signature" class="signature-img">
                                            <?php else: ?>
                                                <span class="text-muted">No signature</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            
    </main>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    // Users table refresh
    function refreshTable() {
        $.get('?ajax=1', function(data) {
            $('#users-table').html(data);
        });
    }
    setInterval(refreshTable, 2000);

    // Chart
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: <?php 
                $datasets = [];
                foreach ($user_hours as $user_id => $user_data) {
                    $borderColorR = rand(0, 255);
                    $borderColorG = rand(0, 255);
                    $borderColorB = rand(0, 255);
                    
                    $datasets[] = [
                        'label' => $user_data['name'],
                        'data' => $user_data['hours'],
                        'fill' => true,
                        'borderColor' => "rgb($borderColorR, $borderColorG, $borderColorB)",
                        'backgroundColor' => "rgba($borderColorR, $borderColorG, $borderColorB, 0.2)",
                        'tension' => 0.1
                    ];
                }
                echo json_encode($datasets);
            ?>
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 8,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create menu toggle button
        const menuToggle = document.createElement('button');
        menuToggle.className = 'menu-toggle';
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(menuToggle);

        // Toggle sidebar
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.sidebar') && 
                !e.target.closest('.menu-toggle') && 
                window.innerWidth <= 1024) {
                document.querySelector('.sidebar').classList.remove('active');
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show dashboard section by default
        document.querySelector('#dashboard').classList.add('active');

        // Handle navigation
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Don't prevent default for logout or links with data-bypass
                if (this.getAttribute('data-bypass') === 'true') {
                    return true;
                }
                
                e.preventDefault();
                
                // Remove active class from all links and sections
                navLinks.forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Show corresponding section
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.add('active');
                }

                // Close sidebar on mobile
                if (window.innerWidth <= 1024) {
                    document.querySelector('.sidebar').classList.remove('active');
                }
            });
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        // Handle view user button click
        $(document).on('click', '.view-user', function() {
            const userId = $(this).data('user-id');
            
            // Fetch user details
            $.get('get_user_details.php', { id: userId }, function(response) {
                const data = JSON.parse(response);
                $('#userDetails').html(data.details);
                $('#userOrders').html(data.orders);
                $('#userModal').modal('show');
            });
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        $('#createUserForm').on('submit', function(e) {
            e.preventDefault();
            const messageDiv = $('#createUserMessage');
            
            $.ajax({
                url: 'admin.php',
                type: 'POST',
                data: $(this).serialize() + '&create_user=1',
                dataType: 'json',
                success: function(response) {
                    messageDiv.removeClass('alert-danger alert-success');
                    
                    if (response.success) {
                        messageDiv.addClass('alert-success');
                        $('#createUserForm')[0].reset();
                        // Refresh the users table
                        refreshTable();
                    } else {
                        messageDiv.addClass('alert-danger');
                    }
                    
                    messageDiv.html(response.message).show();
                    
                    // Hide message after 5 seconds
                    setTimeout(function() {
                        messageDiv.fadeOut();
                    }, 5000);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    messageDiv.removeClass('alert-success')
                        .addClass('alert-danger')
                        .html('An error occurred while creating the user. Please try again.')
                        .show();
                }
            });
        });
    });
    </script>

    <script>
    // Edit User Functionality
    $(document).ready(function() {
        // Handle edit button click
        $(document).on('click', '.edit-user', function() {
            const userId = $(this).data('user-id');
            
            // Fetch user details
            $.ajax({
                url: 'edit_user.php',
                type: 'GET',
                data: { id: userId, ajax: 1 },
                dataType: 'json',
                success: function(data) {
                    // Populate form fields
                    $('#edit_user_id').val(userId);
                    $('#edit_first_name').val(data.first_name);
                    $('#edit_last_name').val(data.last_name);
                    $('#edit_email').val(data.email);
                    $('#edit_role').val(data.role);
                    $('#edit_phone').val(data.phone_number);
                    $('#edit_address').val(data.address);
                    $('#edit_city').val(data.city);
                    $('#edit_postal_code').val(data.postal_code);
                    $('#edit_country').val(data.country);
                    
                    // Show modal
                    $('#editUserModal').modal('show');
                },
                error: function() {
                    alert('Error fetching user data');
                }
            });
        });

        // Handle save changes button click
        $('#saveUserChanges').click(function() {
            const userId = $('#edit_user_id').val();
            const formData = $('#editUserForm').serialize();
            
            $.ajax({
                url: 'edit_user.php?id=' + userId,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    const messageDiv = $('#editUserMessage');
                    messageDiv.removeClass('alert-danger alert-success');
                    
                    if (response.success) {
                        messageDiv.addClass('alert-success');
                        // Refresh the users table
                        location.reload();
                    } else {
                        messageDiv.addClass('alert-danger');
                    }
                    
                    messageDiv.html(response.message).show();
                    
                    // Hide message after 5 seconds
                    setTimeout(function() {
                        messageDiv.fadeOut();
                    }, 5000);
                },
                error: function() {
                    $('#editUserMessage')
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .html('An error occurred while updating the user.')
                        .show();
                }
            });
        });
    });
    </script>


   
    
</body>
</html>
