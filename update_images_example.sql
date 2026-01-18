-- ============================================
-- EXEMPLE : Comment mettre à jour les images dans la base de données
-- ============================================

USE la_ferme;

-- ============================================
-- MÉTHODE 1 : Mettre à jour une image d'un produit spécifique
-- ============================================

-- Exemple : Changer l'image du produit avec l'ID 1
UPDATE products 
SET image = 'nouveau_nom_image.jpg' 
WHERE id = 1;

-- ============================================
-- MÉTHODE 2 : Mettre à jour plusieurs images en une fois
-- ============================================

UPDATE products 
SET image = CASE id
    WHEN 1 THEN 'poussin_chair.jpg'
    WHEN 2 THEN 'poussin_pondeuse.jpg'
    WHEN 3 THEN 'poussin_bleu_holland.jpg'
    WHEN 4 THEN 'poussin_gouliate.jpg'
    WHEN 5 THEN 'poulet_fermier.jpg'
    WHEN 6 THEN 'canard_barbarie.jpg'
    WHEN 7 THEN 'aliment_demarrage.jpg'
    WHEN 8 THEN 'aliment_croissance.jpg'
    WHEN 9 THEN 'aliment_ponte.jpg'
    WHEN 10 THEN 'mais_concasse.jpg'
    WHEN 11 THEN 'mangeoire_auto.jpg'
    WHEN 12 THEN 'abreuvoir_tetine.jpg'
    WHEN 13 THEN 'pondeuse.jpg'
    WHEN 14 THEN 'grillage.jpg'
    WHEN 15 THEN 'oeufs_frais.jpg'
    WHEN 16 THEN 'oeufs_canard.jpg'
    WHEN 17 THEN 'oeufs_caille.jpg'
    WHEN 18 THEN 'poulet_prepare.jpg'
END
WHERE id BETWEEN 1 AND 18;

-- ============================================
-- MÉTHODE 3 : Vérifier quelles images sont manquantes
-- ============================================

-- Voir tous les produits avec leur image
SELECT id, name, image, 
       CASE 
           WHEN image IS NULL OR image = '' THEN '⚠️ Pas d\'image'
           ELSE '✅ Image définie'
       END AS statut_image
FROM products
ORDER BY id;

-- Voir les produits sans image
SELECT id, name 
FROM products 
WHERE image IS NULL OR image = '';

-- ============================================
-- MÉTHODE 4 : Mettre à jour les images des catégories
-- ============================================

UPDATE categories 
SET image = CASE id
    WHEN 1 THEN 'category_poussins.jpg'
    WHEN 2 THEN 'category_aliments.jpg'
    WHEN 3 THEN 'category_materiel.jpg'
    WHEN 4 THEN 'category_oeufs.jpg'
END
WHERE id BETWEEN 1 AND 4;

-- ============================================
-- MÉTHODE 5 : Vérifier toutes les références d'images
-- ============================================

-- Produits avec leurs images
SELECT 
    p.id,
    p.name AS produit,
    p.image AS image_produit,
    c.name AS categorie,
    c.image AS image_categorie
FROM products p
LEFT JOIN categories c ON c.id = p.category_id
ORDER BY p.id;

-- ============================================
-- MÉTHODE 6 : Réinitialiser une image (mettre NULL ou vide)
-- ============================================

-- Supprimer l'image d'un produit (le mettre à NULL)
UPDATE products 
SET image = NULL 
WHERE id = 1;

-- OU mettre une chaîne vide
UPDATE products 
SET image = '' 
WHERE id = 1;

-- ============================================
-- MÉTHODE 7 : Remplacer toutes les images par un nouveau format
-- ============================================

-- Exemple : Remplacer toutes les extensions .jpg par .png
-- ATTENTION : Cette requête nécessite MySQL 8.0+ avec REGEXP_REPLACE
-- UPDATE products 
-- SET image = REGEXP_REPLACE(image, '\\.jpg$', '.png')
-- WHERE image LIKE '%.jpg';

-- Pour MySQL 5.7 ou moins, utiliser REPLACE
-- UPDATE products 
-- SET image = REPLACE(image, '.jpg', '.png')
-- WHERE image LIKE '%.jpg';

-- ============================================
-- CONSEILS D'UTILISATION
-- ============================================

/*
1. Toujours tester d'abord avec un seul produit :
   UPDATE products SET image = 'test.jpg' WHERE id = 1;

2. Vérifier le résultat :
   SELECT id, name, image FROM products WHERE id = 1;

3. Si c'est correct, appliquer aux autres produits.

4. N'oubliez pas de placer les fichiers images dans :
   public/assets/images/

5. Le nom du fichier dans la base de données doit correspondre
   exactement au nom du fichier sur le disque (attention à la casse).
*/

