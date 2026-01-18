<?php
require_once __DIR__ . '/../includes/header.php';

if (!is_logged_in()) {
    // On force la connexion pour passer commande
    redirect('login.php');
}

$pdo = getPDO();
$cart = get_cart();

if (empty($cart)) {
    echo '<p>Votre panier est vide.</p>';
    echo '<p><a href="boutique.php" class="btn-secondary">Retour à la boutique</a></p>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Récupération des informations utilisateur
$userStmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$userStmt->execute([$_SESSION['user']['id']]);
$userInfo = $userStmt->fetch();

// Récupération des produits du panier
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();

$total = 0;
foreach ($products as &$product) {
    $qty = $cart[$product['id']]['quantity'] ?? 0;
    $product['quantity'] = $qty;
    $product['line_total'] = $qty * $product['price'];
    $total += $product['line_total'];
}

$success = false;
$error = '';
$order_id = null;
$selected_payment_method = '';

// Valeurs par défaut du formulaire
$customer_name = $userInfo['name'] ?? '';
$customer_phone = $userInfo['phone'] ?? '';
$customer_address = $userInfo['address'] ?? '';
$payment_method = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    }
    // Validation des champs
    if (empty($customer_name)) {
        $error = 'Le nom complet est obligatoire.';
    } elseif (empty($customer_phone)) {
        $error = 'Le numéro de téléphone est obligatoire.';
    } elseif (empty($customer_address)) {
        $error = 'L\'adresse de livraison est obligatoire.';
    } elseif (empty($payment_method) || !in_array($payment_method, ['orange_money', 'wave', 'livraison'])) {
        $error = 'Veuillez sélectionner un mode de paiement.';
    } else {
        // Vérification du stock avant de créer la commande
        $stockErrors = [];
        foreach ($products as $product) {
        $qty = $product['quantity'];
        $currentStock = (int)$product['stock'];
        
        if ($currentStock < $qty) {
            $stockErrors[] = sprintf(
                'Le produit "%s" n\'a que %d unité(s) en stock, vous en avez demandé %d.',
                htmlspecialchars($product['name']),
                $currentStock,
                $qty
            );
        }
    }
    
        // Vérification du stock avant de créer la commande
        $stockErrors = [];
        foreach ($products as $product) {
            $qty = $product['quantity'];
            $currentStock = (int)$product['stock'];
            
            if ($currentStock < $qty) {
                $stockErrors[] = sprintf(
                    'Le produit "%s" n\'a que %d unité(s) en stock, vous en avez demandé %d.',
                    htmlspecialchars($product['name']),
                    $currentStock,
                    $qty
                );
            }
        }
        
        // Si des erreurs de stock, on affiche les erreurs
        if (!empty($stockErrors)) {
            $error = 'Stock insuffisant pour certains produits :<br>' . implode('<br>', $stockErrors);
        } else {
            // Dans un vrai site, on aurait ici la gestion du paiement.
            $pdo->beginTransaction();
            try {
                // Vérifier si le champ payment_method existe, sinon utiliser un champ alternatif
                try {
                    // Création de la commande avec les informations de livraison et paiement
                    $stmt = $pdo->prepare('INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, total_amount, status, payment_method, created_at) 
                                           VALUES (:user_id, :customer_name, :customer_phone, :customer_address, :total_amount, :status, :payment_method, NOW())');
                    $stmt->execute([
                        'user_id' => $_SESSION['user']['id'],
                        'customer_name' => $customer_name,
                        'customer_phone' => $customer_phone,
                        'customer_address' => $customer_address,
                        'total_amount' => $total,
                        'status' => 'en_attente',
                        'payment_method' => $payment_method,
                    ]);
                } catch (PDOException $e) {
                    // Si le champ payment_method n'existe pas, utiliser une requête sans ce champ
                    // et stocker le mode de paiement dans customer_address (temporaire)
                    $stmt = $pdo->prepare('INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, total_amount, status, created_at) 
                                           VALUES (:user_id, :customer_name, :customer_phone, :customer_address, :total_amount, :status, NOW())');
                    $addressWithPayment = $customer_address . "\n\nMode de paiement: " . $payment_method;
                    $stmt->execute([
                        'user_id' => $_SESSION['user']['id'],
                        'customer_name' => $customer_name,
                        'customer_phone' => $customer_phone,
                        'customer_address' => $addressWithPayment,
                        'total_amount' => $total,
                        'status' => 'en_attente',
                    ]);
                    $order_id = $pdo->lastInsertId();
                    $selected_payment_method = $payment_method;
                }

            $order_id = $pdo->lastInsertId();
            $selected_payment_method = $payment_method;

            // Insertion des articles de commande et diminution du stock
            $stmtItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) 
                                       VALUES (:order_id, :product_id, :quantity, :unit_price, :total_price)');
            
            $stmtUpdateStock = $pdo->prepare('UPDATE products SET stock = stock - :quantity WHERE id = :product_id');

            foreach ($products as $product) {
                // Insertion de l'article de commande
                $stmtItem->execute([
                    'order_id' => $order_id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['price'],
                    'total_price' => $product['line_total'],
                ]);
                
                // Diminution du stock
                $stmtUpdateStock->execute([
                    'quantity' => $product['quantity'],
                    'product_id' => $product['id'],
                ]);
            }

            $pdo->commit();
            clear_cart();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Une erreur est survenue lors de la validation de la commande : " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<div class="checkout-page">
    <div class="checkout-header">
        <h1>Récapitulatif de votre commande</h1>
        <p class="checkout-subtitle">Vérifiez les détails de votre commande avant de confirmer</p>
    </div>

    <?php if (!empty($success)): ?>
        <div class="checkout-success">
            <div class="success-icon">✓</div>
            <h2>Commande confirmée !</h2>
            <p class="success-message">Merci pour votre commande. Le stock a été mis à jour automatiquement.</p>
            <?php if ($order_id): ?>
                <p class="success-info"><strong>Numéro de commande :</strong> #<?php echo $order_id; ?></p>
            <?php endif; ?>
            <?php 
            $payment_names = [
                'orange_money' => 'Orange Money',
                'wave' => 'Wave',
                'livraison' => 'Paiement à la livraison'
            ];
            if ($selected_payment_method && isset($payment_names[$selected_payment_method])): 
            ?>
                <p class="success-info"><strong>Mode de paiement :</strong> <?php echo $payment_names[$selected_payment_method]; ?></p>
            <?php endif; ?>
            <p class="success-info">Vous recevrez un email de confirmation sous peu.</p>
            <div class="success-actions">
                <a href="compte.php" class="btn-primary">Voir mes commandes</a>
                <a href="boutique.php" class="btn-outline">Continuer mes achats</a>
            </div>
        </div>
    <?php else: ?>
        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="checkout-content">
            <div class="checkout-main">
                <!-- Liste des produits -->
                <div class="checkout-products">
                    <h2 class="section-title-checkout">Articles de votre commande</h2>
                    <div class="products-list">
                        <?php foreach ($products as $product): ?>
                            <div class="checkout-product-item">
                                <div class="product-image-checkout">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php else: ?>
                                        <div class="product-placeholder">
                                            <span>📦</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-details-checkout">
                                    <h3 class="product-name-checkout"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <?php if (!empty($product['unit'])): ?>
                                        <p class="product-unit-checkout"><?php echo htmlspecialchars($product['unit']); ?></p>
                                    <?php endif; ?>
                                    <div class="product-stock-checkout">
                                        <span class="product-stock <?php echo $product['stock'] > 10 ? 'stock-available' : ($product['stock'] > 0 ? 'stock-low' : 'stock-out'); ?>">
                                            Stock: <?php echo (int)$product['stock']; ?> disponible(s)
                                        </span>
                                    </div>
                                </div>
                                <div class="product-quantity-checkout">
                                    <span class="quantity-label">Quantité</span>
                                    <span class="quantity-value"><?php echo (int)$product['quantity']; ?></span>
                                </div>
                                <div class="product-price-checkout">
                                    <span class="price-label">Prix unitaire</span>
                                    <span class="price-value"><?php echo number_format($product['price'], 0, ',', ' '); ?> FCFA</span>
                                </div>
                                <div class="product-total-checkout">
                                    <span class="total-label">Total</span>
                                    <span class="total-value"><?php echo number_format($product['line_total'], 0, ',', ' '); ?> FCFA</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Formulaire de commande -->
                <div class="checkout-form-section">
                    <h2 class="section-title-checkout">Informations de livraison et paiement</h2>
                    <form method="post" class="checkout-form">
                        <!-- Coordonnées -->
                        <div class="form-section">
                            <h3 class="form-section-title">📞 Vos coordonnées</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="customer_name">Nom complet *</label>
                                    <input type="text" id="customer_name" name="customer_name" 
                                           value="<?php echo htmlspecialchars($customer_name); ?>" 
                                           required placeholder="Votre nom complet">
                                </div>
                                <div class="form-group">
                                    <label for="customer_phone">Téléphone *</label>
                                    <input type="tel" id="customer_phone" name="customer_phone" 
                                           value="<?php echo htmlspecialchars($customer_phone); ?>" 
                                           required placeholder="+221 XX XXX XX XX">
                                </div>
                            </div>
                        </div>

                        <!-- Adresse de livraison -->
                        <div class="form-section">
                            <h3 class="form-section-title">📍 Adresse de livraison</h3>
                            <div class="form-group">
                                <label for="customer_address">Adresse complète *</label>
                                <textarea id="customer_address" name="customer_address" 
                                          rows="4" required 
                                          placeholder="Rue, quartier, ville, région..."><?php echo htmlspecialchars($customer_address); ?></textarea>
                            </div>
                        </div>

                        <!-- Mode de paiement -->
                        <div class="form-section">
                            <h3 class="form-section-title">💳 Mode de paiement</h3>
                            <div class="payment-methods">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="orange_money" 
                                           <?php echo $payment_method === 'orange_money' ? 'checked' : ''; ?> required>
                                    <div class="payment-card">
                                        <div class="payment-icon">🟠</div>
                                        <div class="payment-info">
                                            <span class="payment-name">Orange Money</span>
                                            <span class="payment-desc">Paiement mobile Orange</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="wave" 
                                           <?php echo $payment_method === 'wave' ? 'checked' : ''; ?> required>
                                    <div class="payment-card">
                                        <div class="payment-icon">💙</div>
                                        <div class="payment-info">
                                            <span class="payment-name">Wave</span>
                                            <span class="payment-desc">Paiement mobile Wave</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="livraison" 
                                           <?php echo $payment_method === 'livraison' ? 'checked' : ''; ?> required>
                                    <div class="payment-card">
                                        <div class="payment-icon">💰</div>
                                        <div class="payment-info">
                                            <span class="payment-name">Paiement à la livraison</span>
                                            <span class="payment-desc">Payez lors de la réception</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary btn-large">
                            <span>✓ Confirmer ma commande</span>
                            <span class="btn-total">Total: <?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Résumé de la commande -->
            <div class="checkout-sidebar">
                <div class="order-summary">
                    <h3 class="summary-title">Résumé de la commande</h3>
                    <div class="summary-content">
                        <div class="summary-row">
                            <span>Nombre d'articles</span>
                            <span><?php echo count($products); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Quantité totale</span>
                            <span><?php echo array_sum(array_column($products, 'quantity')); ?></span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span class="total-amount"><?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
                        </div>
                    </div>
                    <div class="summary-info">
                        <p>📦 Livraison : À l'adresse indiquée</p>
                        <p>💳 Paiement : Sélectionnez votre mode de paiement</p>
                    </div>
                </div>

  
            </div>
            <div class="checkout-help">
                    <h4>Besoin d'aide ?</h4>
                    <p>Si vous avez des questions concernant votre commande, n'hésitez pas à nous contacter.</p>
                    <a href="contact.php" class="btn-outline btn-small">Nous contacter</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


