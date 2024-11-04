<?php
require '../functions/db_conn.php';
session_start();

// Vérifier si l'utilisateur est un admin
if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {
    header("Location: ../index.php");
    exit();
}

// Définir les rôles disponibles
$available_roles = ['admin', 'support', 'user', 'admins'];

// Obtenir les rôles sélectionnés (par défaut, tous les rôles)
$selected_roles = isset($_GET['roles']) ? $_GET['roles'] : $available_roles;

// Obtenir la date de début de la semaine (lundi)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('monday this week'));
$end_date = date('Y-m-d', strtotime($start_date . ' +6 days'));

// Requête pour obtenir les utilisateurs
$sql_users = "SELECT client_id, CONCAT(first_name, ' ', last_name) as full_name, role 
              FROM clients 
              WHERE role IN (" . implode(',', array_fill(0, count($selected_roles), '?')) . ")
              ORDER BY role, last_name";
$stmt_users = $pdo->prepare($sql_users);
$stmt_users->execute($selected_roles);
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

// Obtenir les utilisateurs sélectionnés
$selected_users = isset($_GET['users']) ? $_GET['users'] : array_column($users, 'client_id');

// Requête pour obtenir les statistiques
$sql = "SELECT 
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
            c.role IN (" . implode(',', array_fill(0, count($selected_roles), '?')) . ")
            AND c.client_id IN (" . implode(',', array_fill(0, count($selected_users), '?')) . ")
            AND (us.login_time IS NULL OR (DATE(us.login_time) BETWEEN ? AND ?))
        GROUP BY 
            c.client_id, DATE(us.login_time)
        ORDER BY 
            c.role, c.last_name, date";

$params = array_merge($selected_roles, $selected_users, [$start_date, $end_date]);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Préparer les données pour le graphique et le tableau
$dates = [];
$user_hours = [];
$total_hours = [];

for ($i = 0; $i < 7; $i++) {
    $current_date = date('Y-m-d', strtotime($start_date . ' +' . $i . ' days'));
    $dates[] = date('d/m/Y', strtotime($current_date));
}

foreach ($stats as $row) {
    $user_id = $row['client_id'];
    $date = $row['date'];
    $hours = round($row['total_minutes'] / 60, 2);
    
    if (!isset($user_hours[$user_id])) {
        $user_hours[$user_id] = [
            'name' => $row['full_name'],
            'role' => $row['role'],
            'hours' => array_fill(0, 7, 0)
        ];
    }
    
    if ($date !== null) {
        $day_index = array_search(date('d/m/Y', strtotime($date)), $dates);
        if ($day_index !== false) {
            $user_hours[$user_id]['hours'][$day_index] = $hours;
        }
    }
    
    if (!isset($total_hours[$user_id])) {
        $total_hours[$user_id] = 0;
    }
    $total_hours[$user_id] += $hours;
}

// Calculer le total des heures pour tous les utilisateurs
$grand_total = array_sum($total_hours);
$grand_total_hours = floor($grand_total);
$grand_total_minutes = round(($grand_total - $grand_total_hours) * 60);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Admin et Support</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 60vh;
            width: 80vw;
            margin: auto;
        }
    </style>
</head>
<body>
<?php include 'includes/_nav.php'; ?>
    <div class="container mt-4">
        <h1 class="text-danger">Temps de log</h1>
        
        <form action="" method="GET" class="mb-3">
            <div class="form-group">
                <label>Filtrer par rôle:</label><br>
                <?php foreach ($available_roles as $role): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="<?php echo $role; ?>" id="role_<?php echo $role; ?>" <?php echo in_array($role, $selected_roles) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="role_<?php echo $role; ?>"><?php echo ucfirst($role); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <label>Filtrer par utilisateur:</label><br>
                <?php foreach ($users as $user): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="users[]" value="<?php echo $user['client_id']; ?>" id="user_<?php echo $user['client_id']; ?>" <?php echo in_array($user['client_id'], $selected_users) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="user_<?php echo $user['client_id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
            <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
        </form>
        
        <div class="d-flex justify-content-between align-items-center my-3">
            <a href="?<?php echo http_build_query(array_merge($_GET, ['start_date' => date('Y-m-d', strtotime($start_date . ' -7 days'))])); ?>" class="btn btn-link">&lt;</a>
            <h2>Semaine du <?php echo date('d/m/Y', strtotime($start_date)); ?></h2>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['start_date' => date('Y-m-d', strtotime($start_date . ' +7 days'))])); ?>" class="btn btn-link">&gt;</a>
        </div>
        <h3>Total: <?php echo $grand_total_hours . 'h' . sprintf('%02d', $grand_total_minutes); ?></h3>
        
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>

        <h2 class="mt-5">Temps total de connexion par utilisateur</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Rôle</th>
                    <th>Heures totales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_hours as $user_id => $user_data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user_data['name']); ?></td>
                        <td><?php echo htmlspecialchars($user_data['role']); ?></td>
                        <td><?php echo number_format($total_hours[$user_id], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [
                <?php foreach ($user_hours as $user_id => $user_data): ?>
                {
                    label: '<?php echo addslashes($user_data['name']); ?>',
                    data: <?php echo json_encode($user_data['hours']); ?>,
                    fill: true,
                    borderColor: 'rgb(<?php echo rand(0,255); ?>, <?php echo rand(0,255); ?>, <?php echo rand(0,255); ?>)',
                    backgroundColor: 'rgba(<?php echo rand(0,255); ?>, <?php echo rand(0,255); ?>, <?php echo rand(0,255); ?>, 0.2)',
                    tension: 0.1
                },
                <?php endforeach; ?>
            ]
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
</body>
</html>