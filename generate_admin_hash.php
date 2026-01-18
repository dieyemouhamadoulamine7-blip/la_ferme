<?php
/**
 * Script pour générer le hash du mot de passe administrateur
 * Exécuter une fois : php generate_admin_hash.php
 * Puis copier le hash dans database.sql
 */

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Mot de passe : $password\n";
echo "Hash généré : $hash\n";
echo "\nCopiez ce hash dans database.sql pour l'utilisateur admin.\n";

