<?php
// Gestion des requêtes AJAX AVANT l'inclusion du header
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    require_once __DIR__ . '/../includes/functions.php';
    
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $response = ['success' => false, 'message' => ''];
    
    if ($action === 'add') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($product_id > 0 && $quantity > 0) {
            add_to_cart($product_id, $quantity);
            $cart = get_cart();
            $cart_count = array_sum(array_column($cart, 'quantity'));
            $response = [
                'success' => true,
                'message' => 'Produit ajouté au panier',
                'cart_count' => $cart_count
            ];
        } else {
            $response['message'] = 'ID produit ou quantité invalide';
        }
    } elseif ($action === 'update') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        if ($product_id > 0) {
            update_cart($product_id, $quantity);
            $response = [
                'success' => true,
                'message' => 'Panier mis à jour'
            ];
        } else {
            $response['message'] = 'ID produit invalide';
        }
    } elseif ($action === 'remove') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        
        if ($product_id > 0) {
            update_cart($product_id, 0);
            $response = [
                'success' => true,
                'message' => 'Produit retiré du panier'
            ];
        } else {
            $response['message'] = 'ID produit invalide';
        }
    }
    
    echo json_encode($response);
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$cart = get_cart();
$products = [];
$total = 0;

if ($cart) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as &$product) {
        $qty = $cart[$product['id']]['quantity'] ?? 0;
        $product['quantity'] = $qty;
        $product['line_total'] = $qty * $product['price'];
        $total += $product['line_total'];
    }
}

// Gestion des formulaires classiques
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update']) && !empty($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $product_id => $qty) {
            update_cart((int)$product_id, (int)$qty);
        }
        redirect('panier.php');
    }

    if (isset($_POST['clear'])) {
        clear_cart();
        redirect('panier.php');
    }
}
?>

<h1>Mon panier</h1>

<?php if (empty($products)): ?>
    <p>Votre panier est vide.</p>
    <p><a href="boutique.php" class="btn-secondary">Continuer mes achats</a></p>
<?php else: ?>
    <form method="post">
        <table class="table">
            <thead>
            <tr>
                <th>Produit</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format($product['price'], 2, ',', ' '); ?> FCFA</td>
                    <td>
                        <input type="number"
                               name="quantities[<?php echo (int)$product['id']; ?>]"
                               value="<?php echo (int)$product['quantity']; ?>"
                               min="0">
                    </td>
                    <td><?php echo number_format($product['line_total'], 2, ',', ' '); ?> FCFA</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total :</strong> <?php echo number_format($total, 2, ',', ' '); ?> FCFA</p>
        <button type="submit" name="update" class="btn-secondary">Mettre à jour le panier</button>
        <button type="submit" name="clear" class="btn-danger">Vider le panier</button>
        <a href="commande.php" class="btn-primary">Passer la commande</a>
    </form>
<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>


