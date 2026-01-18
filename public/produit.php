<?php
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

if (empty($_GET['id'])) {
    redirect('boutique.php');
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name 
                       FROM products p 
                       JOIN categories c ON c.id = p.category_id 
                       WHERE p.id = :id');
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<p>Produit introuvable.</p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($qty < 1) {
        $qty = 1;
    }
    add_to_cart($product['id'], $qty);
    $message = 'Produit ajouté au panier.';
}
?>

<div class="product-detail">
    <div>
        <?php if (!empty($product['image'])): ?>
            <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="product-detail-image">
        <?php else: ?>
            <div class="product-detail-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; height: 400px;">
                <span style="color: #999;">Image non disponible</span>
            </div>
        <?php endif; ?>
    </div>
    <div class="product-detail-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
        
        <div class="product-detail-price">
            <?php echo number_format($product['price'], 2, ',', ' '); ?> FCFA
        </div>
        
        <?php if (!empty($product['unit'])): ?>
            <p><strong>Unité :</strong> <?php echo htmlspecialchars($product['unit']); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($product['stock'])): ?>
            <p class="product-stock <?php echo $product['stock'] > 10 ? 'stock-available' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                <strong>Stock :</strong> 
                <?php if ($product['stock'] > 0): ?>
                    <?php echo (int)$product['stock']; ?> disponible(s)
                <?php else: ?>
                    Rupture de stock
                <?php endif; ?>
            </p>
        <?php endif; ?>
        
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

        <?php if (!empty($message)): ?>
            <p class="alert success"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="post" id="add-to-cart-form" onsubmit="event.preventDefault(); addToCartAjax(<?php echo (int)$product['id']; ?>);">
            <div class="quantity-selector">
                <label for="quantity">Quantité :</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo (int)$product['stock']; ?>">
            </div>
            <button type="submit" class="btn-primary" <?php echo ($product['stock'] ?? 0) <= 0 ? 'disabled' : ''; ?>>
                <?php echo ($product['stock'] ?? 0) > 0 ? 'Ajouter au panier' : 'Rupture de stock'; ?>
            </button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


