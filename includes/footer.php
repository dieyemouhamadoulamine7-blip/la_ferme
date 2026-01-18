    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>La Ferme</h3>
                <p>Votre partenaire de confiance pour tous vos besoins avicoles. Des produits de qualité, élevés naturellement.</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>📞 Téléphone: +221 33 890 20 20</p>
                <p>📧 Email: laferme@gmail.com</p>
                <p>📍 Adresse: Sénégal</p>
            </div>
            <div class="footer-section">
                <h3>Liens rapides</h3>
                <a href="<?php echo BASE_URL; ?>">Accueil</a>
                <a href="<?php echo BASE_URL; ?>boutique.php">Boutique</a>
                <a href="<?php echo BASE_URL; ?>panier.php">Panier</a>
                <a href="<?php echo BASE_URL; ?>contact.php">Contact</a>
            </div>
            <div class="footer-section">
                <h3>Informations</h3>
                <a href="<?php echo BASE_URL; ?>compte.php">Mon compte</a>
                <a href="<?php echo BASE_URL; ?>login.php">Connexion</a>
                <a href="<?php echo BASE_URL; ?>register.php">Inscription</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> La Ferme Avicole. Tous droits réservés.</p>
        </div>
    </footer>

    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php else: ?>
        <script src="assets/js/main.js"></script>
    <?php endif; ?>
</body>
</html>


