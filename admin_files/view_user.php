<?php
require '../functions/db_conn.php';
session_start();

// Vérifier si l'utilisateur est connecté et a les droits nécessaires
if (!isset($_SESSION['client_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins' && $_SESSION['user_role'] !== 'support')) {
    header("Location: ../index.php");
    exit();
}

// Vérifier si un ID utilisateur est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage.php");
    exit();
}

$user_id = intval($_GET['id']);

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: manage.php");
    exit();
}

// Récupérer les commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM orders WHERE client_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir l'utilisateur</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/_nav.php'; ?>

    <div class="container mt-4">
        <h1>Détails de l'utilisateur</h1>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="card-text"><strong>Rôle:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                <p class="card-text"><strong>Téléphone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
                <p class="card-text"><strong>Adresse:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                <p class="card-text"><strong>Ville:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
                <p class="card-text"><strong>Code postal:</strong> <?php echo htmlspecialchars($user['postal_code']); ?></p>
                <p class="card-text"><strong>Pays:</strong> <?php echo htmlspecialchars($user['country']); ?></p>
                <p class="card-text"><strong>Date de création:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
            </div>
        </div>

        <h2>Commandes de l'utilisateur</h2>
        <?php if (count($orders) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Montant total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['total_amount']); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune commande trouvée pour cet utilisateur.</p>
        <?php endif; ?>

        <a href="manage.php" class="btn btn-primary mt-3">Retour à la liste des utilisateurs</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>