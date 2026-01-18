<?php
require_once __DIR__ . '/../includes/header.php';
$pdo = getPDO();

// Récupération des catégories
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

// Récupération des produits (option : filtrer par catégorie via ?cat=ID)
$params = [];
$sql = 'SELECT p.*, c.name AS category_name 
        FROM products p 
        JOIN categories c ON c.id = p.category_id 
        WHERE 1=1';

if (!empty($_GET['cat'])) {
    $sql .= ' AND c.id = :cat';
    $params['cat'] = (int)$_GET['cat'];
}

$sql .= ' ORDER BY p.name';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<h1>Boutique</h1>

<section class="filters">
    <form method="get">
        <label for="cat">Catégorie :</label>
        <select name="cat" id="cat" onchange="this.form.submit()">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo (int)$cat['id']; ?>" <?php echo (!empty($_GET['cat']) && (int)$_GET['cat'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</section>

<?php if (empty($products)): ?>
    <p>Aucun produit trouvé.</p>
<?php else: ?>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                <?php else: ?>
                    <div class="product-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; height: 200px;">
                        <span style="color: #999;">Image non disponible</span>
                    </div>
                <?php endif; ?>
                
                <div class="product-info">
                    <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="product-description"><?php echo htmlspecialchars($product['category_name']); ?></p>
                    
                    <?php if (!empty($product['description'])): ?>
                        <p class="product-description"><?php echo htmlspecialchars(mb_substr($product['description'], 0, 100)) . (mb_strlen($product['description']) > 100 ? '...' : ''); ?></p>
                    <?php endif; ?>
                    
                    <p class="product-price"><?php echo number_format($product['price'], 2, ',', ' '); ?> FCFA</p>
                    
                    <?php if (!empty($product['unit'])): ?>
                        <p class="product-unit"><?php echo htmlspecialchars($product['unit']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (isset($product['stock'])): ?>
                        <p class="product-stock <?php echo $product['stock'] > 10 ? 'stock-available' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                            <?php if ($product['stock'] > 0): ?>
                                <?php echo (int)$product['stock']; ?> disponible(s)
                            <?php else: ?>
                                Rupture de stock
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    
                    <a href="produit.php?id=<?php echo (int)$product['id']; ?>" class="btn-primary">Voir le produit</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


