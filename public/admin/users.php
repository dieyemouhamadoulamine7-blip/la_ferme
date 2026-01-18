<?php
require_once __DIR__ . '/../../includes/header.php';

if (!has_role('admin')) {
    redirect('login.php');
}

require_once __DIR__ . '/../../config/db.php';
$pdo = getPDO();

$action = $_GET['action'] ?? 'list';

/* =========================
   CHANGER ROLE
========================= */
if ($action === 'role' && isset($_GET['id']) && isset($_GET['role'])) {
    $id = (int)$_GET['id'];
    $role = $_GET['role'];
    
    // Valider que le rôle est dans l'ENUM
    $validRoles = ['visiteur', 'client', 'admin'];
    if (in_array($role, $validRoles)) {
        $stmt = $pdo->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->execute([$role, $id]);
        redirect('admin/users.php');
        exit;
    }
}

/* =========================
   SUPPRIMER USER
========================= */
if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([ (int)$_GET['id'] ]);
    redirect('admin/users.php');
    exit;
}

/* =========================
   LISTE
========================= */
$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>Gestion des utilisateurs</h1>
            <p class="admin-subtitle">Gérez les comptes utilisateurs et leurs rôles</p>
        </div>
        <div class="admin-header-actions">
            <a href="index.php" class="btn-outline">← Retour au tableau de bord</a>
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div class="admin-empty-state">
            <div class="empty-icon">👥</div>
            <h2>Aucun utilisateur</h2>
            <p>Aucun utilisateur enregistré.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="text-center">#<?= (int)$u['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($u['name']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="role-badge role-<?= htmlspecialchars($u['role']) ?>">
                                    <?php
                                    $roleNames = ['admin' => '👑 Admin', 'client' => '👤 Client', 'visiteur' => '👁️ Visiteur'];
                                    echo $roleNames[$u['role']] ?? htmlspecialchars($u['role']);
                                    ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <div class="role-dropdown">
                                        <button class="btn-action btn-role">🔄 Changer rôle</button>
                                        <div class="role-menu">
                                            <a href="users.php?action=role&id=<?= $u['id'] ?>&role=admin" class="role-option">👑 Admin</a>
                                            <a href="users.php?action=role&id=<?= $u['id'] ?>&role=client" class="role-option">👤 Client</a>
                                            <a href="users.php?action=role&id=<?= $u['id'] ?>&role=visiteur" class="role-option">👁️ Visiteur</a>
                                        </div>
                                    </div>
                                    <a href="users.php?action=delete&id=<?= $u['id'] ?>" 
                                       class="btn-action btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                                       title="Supprimer">
                                        🗑️ Supprimer
                                    </a>
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
