<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// Calculer le nombre d'articles dans le panier
$cart = get_cart();
$cart_count = 0;
if (!empty($cart)) {
    $cart_count = array_sum(array_column($cart, 'quantity'));
}
?>
<header>
    <nav>
        <a href="<?php echo BASE_URL; ?>" class="logo">La Ferme</a>
        <ul class="nav-links">
            <li><a href="<?php echo BASE_URL; ?>">Accueil</a></li>
            <li><a href="<?php echo BASE_URL; ?>boutique.php">Boutique</a></li>
            <li><a href="<?php echo BASE_URL; ?>contact.php">Contact</a></li>
            <?php if (!empty($_SESSION['user'])): ?>
                <li><a href="<?php echo BASE_URL; ?>compte.php">Mon compte</a></li>
                <?php if (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <li><a href="<?php echo BASE_URL; ?>admin/index.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="<?php echo BASE_URL; ?>logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="<?php echo BASE_URL; ?>login.php">Connexion</a></li>
                <li><a href="<?php echo BASE_URL; ?>register.php">Inscription</a></li>
            <?php endif; ?>
            <li>
                <a href="<?php echo BASE_URL; ?>panier.php" class="cart-link">
                    Panier
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </nav>
</header>


