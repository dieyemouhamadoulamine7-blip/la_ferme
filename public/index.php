<?php require_once '../includes/header.php'; ?>

<!-- HERO SLIDER -->
<div class="hero">
    <div class="hero-slide active" style="background-image: url('assets/images/slide1.jpg');"></div>
    <div class="hero-slide" style="background-image: url('assets/images/slide2.jpg');"></div>
    <div class="hero-slide" style="background-image: url('assets/images/slide3.jpg');"></div>
    <div class="hero-slide" style="background-image: url('assets/images/slide4.jpg');"></div>

    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1>Bienvenue à la Ferme Avicole</h1>
        <p>Des produits avicoles de qualité, élevés naturellement</p>
        <a href="boutique.php" class="btn-main">Voir la boutique</a>
    </div>
</div>

<script>
let slides = document.querySelectorAll('.hero-slide');
let index = 0;

setInterval(() => {
    slides[index].classList.remove('active');
    index = (index + 1) % slides.length;
    slides[index].classList.add('active');
}, 4000);
</script>

<!-- POURQUOI NOUS -->
<section class="section featured-section">
    <h2 class="section-title">Pourquoi nous choisir ?</h2>
    <div class="features">
        <div class="feature-box">
            <h3>🐔 Qualité</h3>
            <p>Nos poulets sont élevés naturellement et soigneusement.</p>
        </div>
        <div class="feature-box">
            <h3>🚚 Livraison</h3>
            <p>Livraison rapide partout au Sénégal.</p>
        </div>
        <div class="feature-box">
            <h3>✅ Confiance</h3>
            <p>Des centaines de clients satisfaits.</p>
        </div>
    </div>
</section>

<!-- PRODUITS POPULAIRES -->
<section class="featured-section">
    <h2 class="section-title">Produits en vedette</h2>
    <div class="products-grid">
        <?php
        require_once __DIR__ . '/../config/db.php';
        $pdo = getPDO();

        $stmt = $pdo->query("SELECT * FROM products LIMIT 4");
        while ($p = $stmt->fetch()) {
        ?>
            <article class="product-card">
                <?php if (!empty($p['image'])): ?>
                    <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($p['image']); ?>" 
                         alt="<?php echo htmlspecialchars($p['name']); ?>" 
                         class="product-image">
                <?php else: ?>
                    <div class="product-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #999;">Image non disponible</span>
                    </div>
                <?php endif; ?>
                <div class="product-info">
                    <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <p class="product-price"><?php echo number_format($p['price'], 2, ',', ' '); ?> FCFA</p>
                    <a href="produit.php?id=<?php echo (int)$p['id']; ?>" class="btn-primary">Voir le produit</a>
                </div>
            </article>
        <?php } ?>
    </div>
</section>



<?php require_once '../includes/footer.php'; ?>