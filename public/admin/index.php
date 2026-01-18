<?php
require_once __DIR__ . '/../../includes/header.php';

if (!has_role('admin')) {
    redirect('login.php');
}

require_once __DIR__ . '/../../config/db.php';
$pdo = getPDO();

// Statistiques
$stats = [
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'en_attente'")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'annulee'")->fetchColumn(),
];

// Commandes récentes
$recentOrders = $pdo->query("
    SELECT o.*, u.name AS user_name 
    FROM orders o 
    LEFT JOIN users u ON u.id = o.user_id 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetchAll();
?>

<div class="admin-dashboard">
    <div class="admin-header-section">
        <div>
            <h1>Tableau de bord</h1>
            <p class="admin-subtitle">Bienvenue dans l'administration de La Ferme</p>
        </div>
        <div class="admin-header-actions">
            <a href="<?php echo BASE_URL; ?>" class="btn-outline" target="_blank">Voir le site</a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card stat-primary">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-value"><?php echo (int)$stats['products']; ?></div>
                <div class="stat-label">Produits</div>
            </div>
            <a href="products.php" class="stat-link">Voir tous →</a>
        </div>

        <div class="admin-stat-card stat-success">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <div class="stat-value"><?php echo (int)$stats['categories']; ?></div>
                <div class="stat-label">Catégories</div>
            </div>
            <a href="categories.php" class="stat-link">Voir toutes →</a>
        </div>

        <div class="admin-stat-card stat-warning">
            <div class="stat-icon">🛒</div>
            <div class="stat-content">
                <div class="stat-value"><?php echo (int)$stats['orders']; ?></div>
                <div class="stat-label">Commandes</div>
            </div>
            <a href="orders.php" class="stat-link">Voir toutes →</a>
        </div>

        <div class="admin-stat-card stat-info">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-value"><?php echo (int)$stats['users']; ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <a href="users.php" class="stat-link">Voir tous →</a>
        </div>

        <div class="admin-stat-card stat-danger">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <div class="stat-value"><?php echo (int)$stats['pending_orders']; ?></div>
                <div class="stat-label">En attente</div>
            </div>
            <a href="orders.php?filter=pending" class="stat-link">Voir →</a>
        </div>

        <div class="admin-stat-card stat-revenue">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?></div>
                <div class="stat-label">FCFA (CA total)</div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="admin-quick-actions">
        <h2 class="admin-section-title">Actions rapides</h2>
        <div class="quick-actions-grid">
            <a href="products.php?action=add" class="quick-action-card">
                <div class="quick-action-icon">➕</div>
                <div class="quick-action-title">Ajouter un produit</div>
                <div class="quick-action-desc">Créer un nouveau produit</div>
            </a>

            <a href="categories.php?action=add" class="quick-action-card">
                <div class="quick-action-icon">📁</div>
                <div class="quick-action-title">Ajouter une catégorie</div>
                <div class="quick-action-desc">Créer une nouvelle catégorie</div>
            </a>

            <a href="orders.php" class="quick-action-card">
                <div class="quick-action-icon">📊</div>
                <div class="quick-action-title">Voir les commandes</div>
                <div class="quick-action-desc">Gérer les commandes</div>
            </a>

            <a href="users.php" class="quick-action-card">
                <div class="quick-action-icon">👤</div>
                <div class="quick-action-title">Gérer les utilisateurs</div>
                <div class="quick-action-desc">Voir et modifier les utilisateurs</div>
            </a>
        </div>
    </div>

    <!-- Commandes récentes -->
    <?php if (!empty($recentOrders)): ?>
    <div class="admin-recent-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Commandes récentes</h2>
            <a href="orders.php" class="btn-outline btn-small">Voir toutes</a>
        </div>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>#<?php echo (int)$order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['user_name'] ?? 'Invité'); ?></td>
                        <td><?php echo number_format($order['total_amount'] ?? $order['total'] ?? 0, 0, ',', ' '); ?> FCFA</td>
                        <td>
                            <span class="status-badge status-<?php echo htmlspecialchars($order['status']); ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="orders.php?id=<?php echo (int)$order['id']; ?>" class="btn-action btn-view">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>


