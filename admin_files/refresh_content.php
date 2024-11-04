<?php
require '../functions/db_conn.php';

// Requête pour obtenir l'historique des 5 derniers supports connectés
$sql_last_connected = "SELECT c.client_id, c.first_name, c.last_name, c.email, us.login_time, 
        COALESCE(us.logout_time, us.signature_time, us.login_time) as last_activity
        FROM clients c
        JOIN user_sessions us ON c.client_id = us.user_id
        WHERE c.role = 'support'
        ORDER BY us.login_time DESC
        LIMIT 5";

$stmt_last_connected = $pdo->prepare($sql_last_connected);
$stmt_last_connected->execute();
$last_connected_support = $stmt_last_connected->fetchAll(PDO::FETCH_ASSOC);

// Requête pour obtenir les utilisateurs support actuellement connectés
$sql = "SELECT c.client_id, c.first_name, c.last_name, c.email, us.login_time, 
        COALESCE(us.signature_time, us.login_time) as last_activity,
        us.signature_image as current_signature
        FROM clients c
        JOIN user_sessions us ON c.client_id = us.user_id
        WHERE c.role = 'support' AND us.logout_time IS NULL
        ORDER BY last_activity DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$connected_support = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Historique des 5 Derniers Supports Connectés</h2>
<?php if (empty($last_connected_support)): ?>
    <p class="alert alert-info">Aucun historique de connexion disponible.</p>
<?php else: ?>
    <div class="table-responsive mb-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Dernière connexion</th>
                    <th>Dernière activité</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($last_connected_support as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($user['login_time'])); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($user['last_activity'])); ?></td>
                        <td>
                            <?php if ($user['last_activity'] == $user['login_time']): ?>
                                <span class="badge badge-success">Connecté</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Déconnecté</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<h2>Supports Actuellement Connectés</h2>
<?php if (empty($connected_support)): ?>
    <p class="alert alert-info">Aucun utilisateur support n'est actuellement connecté.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Connecté depuis</th>
                    <th>Dernière activité</th>
                    <th>Signature actuelle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($connected_support as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($user['login_time'])); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($user['last_activity'])); ?></td>
                        <td>
                            <?php if ($user['current_signature']): ?>
                                <img src="data:image/png;base64,<?php echo base64_encode($user['current_signature']); ?>" alt="Signature" class="signature-img">
                            <?php else: ?>
                                <span class="text-muted">Pas de signature</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>