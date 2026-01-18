# Guide : Comment référencer les images dans la base de données

## 📁 Structure des dossiers

Les images doivent être placées dans :
```
public/assets/images/
```

## 🖼️ Format des images dans la base de données

Dans la base de données, le champ `image` des tables `products` et `categories` doit contenir **uniquement le nom du fichier** (pas le chemin complet).

### ✅ Exemples corrects

```sql
-- Dans la table products
UPDATE products SET image = 'poussin_chair.jpg' WHERE id = 1;

-- Dans la table categories  
UPDATE categories SET image = 'category_poussins.jpg' WHERE id = 1;
```

### ❌ Exemples incorrects (à éviter)

```sql
-- Ne PAS mettre le chemin complet
UPDATE products SET image = '/public/assets/images/poussin_chair.jpg' WHERE id = 1;

-- Ne PAS mettre l'URL complète
UPDATE products SET image = 'http://localhost/la_ferme/public/assets/images/poussin_chair.jpg' WHERE id = 1;
```

## 🔧 Comment ajouter/modifier une image dans la base de données

### Méthode 1 : Via phpMyAdmin (Interface graphique)

1. Ouvrir phpMyAdmin : http://localhost/phpmyadmin
2. Sélectionner la base de données `la_ferme`
3. Cliquer sur la table `products` (ou `categories`)
4. Cliquer sur "Modifier" pour éditer un produit
5. Dans le champ `image`, mettre uniquement le nom du fichier : `mon_image.jpg`
6. Cliquer sur "Exécuter"

### Méthode 2 : Via SQL direct

```sql
-- Mettre à jour l'image d'un produit existant
UPDATE products 
SET image = 'nouveau_nom.jpg' 
WHERE id = 1;

-- Mettre à jour l'image d'une catégorie
UPDATE categories 
SET image = 'categorie_image.jpg' 
WHERE id = 1;
```

### Méthode 3 : Via l'interface d'administration (à venir)

L'interface admin permettra bientôt de télécharger et gérer les images directement depuis le site.

## 📋 Liste des images référencées dans database.sql

### Images de produits

- `poussin_chair.jpg` - Poussins de chair
- `poussin_pondeuse.jpg` - Poussins pondeuses
- `poulet_fermier.jpg` - Poulets fermiers
- `canard_barbarie.jpg` - Canards de Barbarie
- `aliment_demarrage.jpg` - Aliment démarrage
- `aliment_croissance.jpg` - Aliment croissance
- `aliment_ponte.jpg` - Aliment ponte
- `mais_concasse.jpg` - Maïs concassé
- `mangeoire_auto.jpg` - Mangeoire automatique
- `abreuvoir_tetine.jpg` - Abreuvoir à tétine
- `pondeuse.jpg` - Pondeuse
- `grillage.jpg` - Grillage avicole
- `oeufs_frais.jpg` - Œufs frais fermiers
- `oeufs_canard.jpg` - Œufs de canard
- `oeufs_caille.jpg` - Œufs de caille
- `poulet_prepare.jpg` - Poulet préparé

### Images de catégories

- `category_poussins.jpg` - Catégorie Poussins & volailles
- `category_aliments.jpg` - Catégorie Aliments avicoles
- `category_materiel.jpg` - Catégorie Matériel avicole
- `category_oeufs.jpg` - Catégorie Œufs & produits frais

## 🎯 Format recommandé pour les noms de fichiers

- Utiliser des caractères alphanumériques et underscores (`_`)
- Éviter les espaces et caractères spéciaux
- Utiliser des minuscules
- Format : `nom_produit.jpg` ou `nom_produit.png`

Exemples :
- ✅ `poussin_chair.jpg`
- ✅ `oeufs_frais_fermiers.jpg`
- ❌ `Poussin Chair.JPG` (éviter majuscules et espaces)
- ❌ `poussin-chair.jpg` (préférer underscore)

## 📐 Tailles d'images recommandées

### Images produits
- **Largeur recommandée** : 800-1200px
- **Ratio** : 4:3 ou 16:9
- **Format** : JPG (qualité 80-85%) ou PNG (pour transparence)
- **Poids** : < 500KB pour un chargement rapide

### Images catégories
- **Largeur recommandée** : 600-800px
- **Ratio** : 16:9 ou 3:2
- **Format** : JPG
- **Poids** : < 300KB

## 🔍 Comment le code utilise les images

### Dans les pages PHP

```php
// Exemple dans produit.php
<?php if (!empty($product['image'])): ?>
    <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
         alt="<?php echo htmlspecialchars($product['name']); ?>">
<?php endif; ?>
```

**Explication** :
- `BASE_URL` = `http://localhost/la_ferme/public/`
- `assets/images/` = le dossier des images
- `$product['image']` = le nom du fichier depuis la base de données
- **Résultat final** : `http://localhost/la_ferme/public/assets/images/poussin_chair.jpg`

## 🛠️ Script SQL pour ajouter une image

```sql
-- Exemple : Ajouter une image à un produit existant
UPDATE products 
SET image = 'nom_image.jpg' 
WHERE id = 5;

-- Exemple : Ajouter plusieurs images à la fois
UPDATE products 
SET image = CASE id
    WHEN 1 THEN 'poussin_chair.jpg'
    WHEN 2 THEN 'poussin_pondeuse.jpg'
    WHEN 3 THEN 'poulet_fermier.jpg'
END
WHERE id IN (1, 2, 3);
```

## 📝 Checklist avant de mettre une image

- [ ] L'image est placée dans `public/assets/images/`
- [ ] Le nom du fichier dans la base de données correspond exactement au nom du fichier
- [ ] Le nom du fichier n'a pas d'espaces ni de caractères spéciaux
- [ ] L'image a une taille raisonnable (< 500KB)
- [ ] L'extension est correcte (.jpg, .jpeg, .png, .webp)

## 🐛 Résolution de problèmes

### L'image ne s'affiche pas

1. **Vérifier que le fichier existe** :
   - Aller dans `public/assets/images/`
   - Vérifier que le nom du fichier correspond exactement (attention à la casse)

2. **Vérifier le nom dans la base de données** :
   ```sql
   SELECT id, name, image FROM products WHERE id = 1;
   ```

3. **Vérifier les permissions** :
   - Le dossier `images` doit être en lecture

4. **Vérifier le chemin dans le navigateur** :
   - Ouvrir les outils de développement (F12)
   - Onglet "Réseau" ou "Network"
   - Recharger la page et vérifier l'URL de l'image

### Image tronquée ou mal dimensionnée

- Vérifier les styles CSS dans `public/assets/css/style.css`
- Les classes `.product-image` et `.product-detail-image` définissent les dimensions

## 💡 Bonnes pratiques

1. **Optimiser les images** avant de les mettre en ligne (compression)
2. **Utiliser des noms descriptifs** : `poussin_chair.jpg` plutôt que `img1.jpg`
3. **Maintenir une cohérence** : utiliser le même format (JPG) pour toutes les images produits
4. **Créer des miniatures** si nécessaire pour améliorer les performances
5. **Backup régulier** : sauvegarder le dossier `images` avec la base de données

## 📚 Ressources utiles

- [Optimiseur d'images en ligne](https://tinypng.com/)
- [Convertisseur d'images](https://convertio.co/)
- [Guide PHP : upload d'images](https://www.php.net/manual/fr/features.file-upload.php)

---

**Note** : Si vous ajoutez de nouvelles images, pensez à mettre à jour ce guide avec la liste complète !

