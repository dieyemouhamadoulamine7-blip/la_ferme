<?php
require_once __DIR__ . '/../includes/header.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$pdo = getPDO();

// Récupérer les commandes de l'utilisateur connecté
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
$stmt->execute(['user_id' => $_SESSION['user']['id']]);
$orders = $stmt->fetchAll();
?>

<h1>Mon compte</h1>

<section>
    <h2>Mes informations</h2>
    <p><strong>Nom :</strong> <?php echo htmlspecialchars($_SESSION['user']['name']); ?></p>
    <p><strong>Email :</strong> <?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
</section>

<section>
    <h2>Mes commandes</h2>
    <?php if (empty($orders)): ?>
        <p>Vous n'avez pas encore passé de commande.</p>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>
                <th>Numéro</th>
                <th>Date</th>
                <th>Montant</th>
                <th>Statut</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo (int)$order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                    <td><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> FCFA</td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


