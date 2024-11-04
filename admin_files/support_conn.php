<?php
require '../functions/db_conn.php';
session_start();

// Vérifier si l'utilisateur est un admin
if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {
    header("Location: ../index.php");
    exit();
}

// Récupérer la liste des supports
$sql_supports = "SELECT client_id, CONCAT(first_name, ' ', last_name) AS full_name FROM clients WHERE role = 'support'";
$stmt_supports = $pdo->query($sql_supports);
$supports = $stmt_supports->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire de filtre
$selected_support = $_GET['support'] ?? null;
$selected_week = $_GET['week'] ?? date('Y-W');

list($year, $week) = explode('-', $selected_week);
$start_date = date('Y-m-d', strtotime($year . "W" . $week . "1")); // Lundi de la semaine sélectionnée
$end_date = date('Y-m-d', strtotime($year . "W" . $week . "7")); // Dimanche de la semaine sélectionnée

// Requête pour obtenir les signatures de la semaine pour le support sélectionné
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

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Connecté</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .signature-img {
            max-width: 200px;
            max-height: 100px;
            object-fit: contain;
        }
    </style>
</head>
<body>
<?php include 'includes/_nav.php'; ?>
    <div class="container mt-4">
        <h1 class="mb-4">Gestion du Support</h1>
        
        <form class="mb-4" method="GET">
            <div class="form-row">
                <div class="col">
                    <select name="support" class="form-control">
                        <option value="">Sélectionner un support</option>
                        <?php foreach ($supports as $support): ?>
                            <option value="<?php echo $support['client_id']; ?>" <?php echo $selected_support == $support['client_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($support['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col">
                    <input type="week" name="week" class="form-control" value="<?php echo $selected_week; ?>">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </div>
        </form>
        
        <div id="content-to-refresh">
            <!-- Le contenu sera chargé ici par AJAX -->
        </div>

        <?php if ($selected_support): ?>
            <h2 class="mt-5">Historique des Signatures</h2>
            <?php if (!empty($signatures)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date et heure</th>
                                <th>Signature</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($signatures as $signature): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($signature['signature_time'])); ?></td>
                                    <td>
                                        <?php if ($signature['signature_image']): ?>
                                            <img src="data:image/png;base64,<?php echo base64_encode($signature['signature_image']); ?>" alt="Signature" class="signature-img">
                                        <?php else: ?>
                                            <span class="text-muted">Pas de signature</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="alert alert-warning">Aucune signature trouvée pour ce support durant la semaine sélectionnée.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        function refreshContent() {
            $('#content-to-refresh').load('refresh_content.php');
        }
        
        // Rafraîchir le contenu toutes les 2 secondes
        setInterval(refreshContent, 2000);

        // Charger le contenu initial
        refreshContent();
    });
    </script>
</body>
</html>