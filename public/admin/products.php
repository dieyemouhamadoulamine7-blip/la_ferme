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
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    redirect('admin/products.php');
    exit;
}

/* =========================
   AJOUT
========================= */
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    $unit = trim($_POST['unit'] ?? 'unité');
    $image = trim($_POST['image'] ?? '');
    
    if ($category_id > 0 && $name && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, unit, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $unit,
            $image
        ]);
        redirect('admin/products.php');
        exit;
    }
}

/* =========================
   MODIFICATION
========================= */
if ($action === 'edit' && isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_GET['id'];
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    $unit = trim($_POST['unit'] ?? 'unité');
    $image = trim($_POST['image'] ?? '');
    
    if ($category_id > 0 && $name && $price > 0) {
        $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, unit=?, image=? WHERE id=?");
        $stmt->execute([
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $unit,
            $image,
            $id
        ]);
        redirect('admin/products.php');
        exit;
    }
}

/* =========================
   DONNÉES
========================= */
// Récupérer les catégories pour les formulaires
$categoriesStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll();

if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $productToEdit = $stmt->fetch();
}

$stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC");
$products = $stmt->fetchAll();
?>

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>Gestion des produits</h1>
            <p class="admin-subtitle">Gérez tous vos produits depuis cette page</p>
        </div>
        <div class="admin-header-actions">
            <a href="index.php" class="btn-outline">← Retour au tableau de bord</a>
            <a href="products.php?action=add" class="btn-primary">
                <span>➕</span> Ajouter un produit
            </a>
        </div>
    </div>

    <?php if ($action === 'list'): ?>
        <?php if (empty($products)): ?>
            <div class="admin-empty-state">
                <div class="empty-icon">📦</div>
                <h2>Aucun produit</h2>
                <p>Commencez par ajouter votre premier produit.</p>
                <a href="products.php?action=add" class="btn-primary">Ajouter un produit</a>
            </div>
        <?php else: ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="text-center">#<?= (int)$product['id'] ?></td>
                                <td class="product-image-cell">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="product-thumb">
                                    <?php else: ?>
                                        <div class="product-thumb-placeholder">📦</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    <?php if (!empty($product['description'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars(mb_substr($product['description'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-category"><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></span>
                                </td>
                                <td class="text-right">
                                    <strong><?= number_format($product['price'], 0, ',', ' ') ?> FCFA</strong>
                                </td>
                                <td>
                                    <span class="stock-badge <?php echo $product['stock'] > 10 ? 'stock-ok' : ($product['stock'] > 0 ? 'stock-warning' : 'stock-danger'); ?>">
                                        <?= (int)$product['stock'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="products.php?action=edit&id=<?= $product['id'] ?>" 
                                           class="btn-action btn-edit" 
                                           title="Modifier">
                                            ✏️ Modifier
                                        </a>
                                        <a href="products.php?action=delete&id=<?= $product['id'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')"
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
            <h2>Ajouter un produit</h2>
            <a href="products.php" class="btn-outline">← Retour à la liste</a>
        </div>
    <form method="post" class="form-auth">
        <label>Catégorie * :</label>
        <select name="category_id" required>
            <option value="">-- Sélectionner une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Nom * :</label>
        <input type="text" name="name" placeholder="Nom du produit" required>
        
        <label>Description :</label>
        <textarea name="description" placeholder="Description du produit" rows="4"></textarea>
        
        <label>Prix (FCFA) * :</label>
        <input type="number" name="price" step="0.01" placeholder="Prix" required>
        
        <label>Stock * :</label>
        <input type="number" name="stock" placeholder="Stock" min="0" required>
        
        <label>Unité :</label>
        <input type="text" name="unit" placeholder="unité, kg, sac, etc." value="unité">
        
        <label>Image (nom du fichier) :</label>
        <input type="text" name="image" placeholder="nom_image.jpg">
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Enregistrer</button>
            <a href="products.php" class="btn-outline">Annuler</a>
        </div>
    </form>

    <?php elseif ($action === 'edit' && isset($productToEdit)): ?>
        <div class="admin-form-header">
            <h2>Modifier le produit</h2>
            <a href="products.php" class="btn-outline">← Retour à la liste</a>
        </div>
    <form method="post" class="form-auth">
        <label>Catégorie * :</label>
        <select name="category_id" required>
            <option value="">-- Sélectionner une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= ($productToEdit['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label>Nom * :</label>
        <input type="text" name="name" value="<?= htmlspecialchars($productToEdit['name']) ?>" required>
        
        <label>Description :</label>
        <textarea name="description" placeholder="Description du produit" rows="4"><?= htmlspecialchars($productToEdit['description'] ?? '') ?></textarea>
        
        <label>Prix (FCFA) * :</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($productToEdit['price']) ?>" required>
        
        <label>Stock * :</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($productToEdit['stock']) ?>" min="0" required>
        
        <label>Unité :</label>
        <input type="text" name="unit" value="<?= htmlspecialchars($productToEdit['unit'] ?? 'unité') ?>" placeholder="unité, kg, sac, etc.">
        
        <label>Image (nom du fichier) :</label>
        <input type="text" name="image" value="<?= htmlspecialchars($productToEdit['image'] ?? '') ?>" placeholder="nom_image.jpg">
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Modifier</button>
            <a href="products.php" class="btn-outline">Annuler</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
