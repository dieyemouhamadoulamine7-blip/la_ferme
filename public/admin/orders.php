<?php
require_once __DIR__ . '/../../includes/header.php';

if (!has_role('admin')) {
    redirect('login.php');
}

require_once __DIR__ . '/../../config/db.php';
$pdo = getPDO();

$action = $_GET['action'] ?? 'list';

/* =========================
   CHANGER STATUT
========================= */
if ($action === 'status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    
    // Valider que le statut est dans l'ENUM
    $validStatuses = ['en_attente', 'en_cours', 'expediee', 'livree', 'annulee'];
    if (in_array($status, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->execute([$status, $id]);
        redirect('admin/orders.php');
        exit;
    }
}

/* =========================
   LISTE COMMANDES
========================= */
$stmt = $pdo->query("
    SELECT o.*, u.name AS user_name
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>Gestion des commandes</h1>
            <p class="admin-subtitle">Suivez et gérez toutes les commandes</p>
        </div>
        <div class="admin-header-actions">
            <a href="index.php" class="btn-outline">← Retour au tableau de bord</a>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="admin-empty-state">
            <div class="empty-icon">🛒</div>
            <h2>Aucune commande</h2>
            <p>Aucune commande n'a été passée pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Montant</th>
                        <th>Paiement</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td class="text-center">#<?= (int)$o['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($o['user_name'] ?? ($o['customer_name'] ?? 'Invité')) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($o['customer_phone'] ?? 'N/A') ?></td>
                            <td>
                                <small><?= htmlspecialchars(mb_substr($o['customer_address'] ?? '', 0, 40)) ?><?= mb_strlen($o['customer_address'] ?? '') > 40 ? '...' : '' ?></small>
                            </td>
                            <td class="text-right">
                                <strong><?= number_format($o['total_amount'] ?? $o['total'] ?? 0, 0, ',', ' ') ?> FCFA</strong>
                            </td>
                            <td>
                                <?php 
                                $payment = $o['payment_method'] ?? 'N/A';
                                $paymentNames = ['orange_money' => 'Orange Money', 'wave' => 'Wave', 'livraison' => 'À la livraison'];
                                echo htmlspecialchars($paymentNames[$payment] ?? $payment);
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($o['status']) ?>">
                                    <?= htmlspecialchars($o['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <div class="status-dropdown">
                                        <button class="btn-action btn-status">📊 Changer statut</button>
                                        <div class="status-menu">
                                            <a href="orders.php?action=status&id=<?= $o['id'] ?>&status=en_attente" class="status-option">⏳ En attente</a>
                                            <a href="orders.php?action=status&id=<?= $o['id'] ?>&status=en_cours" class="status-option">🔄 En cours</a>
                                            <a href="orders.php?action=status&id=<?= $o['id'] ?>&status=expediee" class="status-option">📦 Expédiée</a>
                                            <a href="orders.php?action=status&id=<?= $o['id'] ?>&status=livree" class="status-option">✅ Livrée</a>
                                            <a href="orders.php?action=status&id=<?= $o['id'] ?>&status=annulee" class="status-option status-danger">❌ Annulée</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
