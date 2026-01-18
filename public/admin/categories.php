<?php
require_once __DIR__ . '/../../includes/header.php';

if (!has_role('admin')) {
    redirect('login.php');
}

require_once __DIR__ . '/../../config/db.php';
$pdo = getPDO();

$action = $_GET['action'] ?? 'list';

/* =========================
   SUPPRESSION
========================= */
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    redirect('admin/categories.php');
    exit;
}

/* =========================
   AJOUT
========================= */
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
        $stmt->execute([
            $name,
            $description ?: null,
            $image ?: null
        ]);
        redirect('admin/categories.php');
        exit;
    }
}

/* =========================
   MODIFICATION
========================= */
if ($action === 'edit' && isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_GET['id'];
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    
    if ($name) {
        $stmt = $pdo->prepare("UPDATE categories SET name=?, description=?, image=? WHERE id=?");
        $stmt->execute([
            $name,
            $description ?: null,
            $image ?: null,
            $id
        ]);
        redirect('admin/categories.php');
        exit;
    }
}

/* =========================
   DONNÉES
========================= */
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$id]);
    $categoryToEdit = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>Gestion des catégories</h1>
            <p class="admin-subtitle">Organisez vos produits par catégories</p>
        </div>
        <div class="admin-header-actions">
            <a href="index.php" class="btn-outline">← Retour au tableau de bord</a>
            <a href="categories.php?action=add" class="btn-primary">
                <span>➕</span> Ajouter une catégorie
            </a>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
        <?php if (empty($categories)): ?>
            <div class="admin-empty-state">
                <div class="empty-icon">📁</div>
                <h2>Aucune catégorie</h2>
                <p>Commencez par créer votre première catégorie.</p>
                <a href="categories.php?action=add" class="btn-primary">Ajouter une catégorie</a>
            </div>
        <?php else: ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="text-center">#<?= (int)$cat['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($cat['name']) ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($cat['description'])): ?>
                                        <?= htmlspecialchars(mb_substr($cat['description'], 0, 80)) ?><?= mb_strlen($cat['description']) > 80 ? '...' : '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">Aucune description</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="categories.php?action=edit&id=<?= $cat['id'] ?>" 
                                           class="btn-action btn-edit" 
                                           title="Modifier">
                                            ✏️ Modifier
                                        </a>
                                        <a href="categories.php?action=delete&id=<?= $cat['id'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')"
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

    <?php elseif ($action === 'add'): ?>
        <div class="admin-form-header">
            <h2>Ajouter une catégorie</h2>
            <a href="categories.php" class="btn-outline">← Retour à la liste</a>
        </div>
    <form method="post" class="form-auth">
        <label>Nom * :</label>
        <input type="text" name="name" placeholder="Nom de la catégorie" required>
        
        <label>Description :</label>
        <textarea name="description" placeholder="Description de la catégorie" rows="4"></textarea>
        
        <label>Image (nom du fichier) :</label>
        <input type="text" name="image" placeholder="nom_image.jpg">
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Enregistrer</button>
            <a href="categories.php" class="btn-outline">Annuler</a>
        </div>
    </form>

    <?php elseif ($action === 'edit' && isset($categoryToEdit)): ?>
        <div class="admin-form-header">
            <h2>Modifier la catégorie</h2>
            <a href="categories.php" class="btn-outline">← Retour à la liste</a>
        </div>
    <form method="post" class="form-auth">
        <label>Nom * :</label>
        <input type="text" name="name" value="<?= htmlspecialchars($categoryToEdit['name']) ?>" required>
        
        <label>Description :</label>
        <textarea name="description" placeholder="Description de la catégorie" rows="4"><?= htmlspecialchars($categoryToEdit['description'] ?? '') ?></textarea>
        
        <label>Image (nom du fichier) :</label>
        <input type="text" name="image" value="<?= htmlspecialchars($categoryToEdit['image'] ?? '') ?>" placeholder="nom_image.jpg">
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Modifier</button>
            <a href="categories.php" class="btn-outline">Annuler</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
