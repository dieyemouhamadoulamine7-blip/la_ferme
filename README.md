# Projet pédagogique e-commerce – "La Ferme"

Ce projet a pour objectif de recréer, à des fins **pédagogiques**, la logique et la structure d'un site e‑commerce type ferme en ligne, en utilisant uniquement notre propre code (HTML, CSS, JavaScript, PHP, MySQL).

## 🚀 Installation

1. **Installer WAMP/XAMPP** (serveur local avec PHP et MySQL)

2. **Créer la base de données** :
   - Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
   - Créer une nouvelle base de données nommée `la_ferme`
   - Importer le fichier `database.sql` dans cette base de données

3. **Configurer la connexion** :
   - Modifier si nécessaire les paramètres dans `config/config.php` et `config/db.php`

4. **Accéder au site** :
   - Ouvrir http://localhost/la_ferme/public/

5. **Compte administrateur par défaut** :
   - Email : `admin@laferme.com`
   - Mot de passe : `admin123`
   - **Important** : Si le mot de passe ne fonctionne pas, générez un nouveau hash :
     - Exécutez : `php generate_admin_hash.php`
     - Copiez le hash généré dans `database.sql` (ligne 131)
     - Réimportez la base de données

## 📁 Structure du projet

```
la_ferme/
├── config/              # Configuration
│   ├── config.php       # Configuration générale (URL, nom du site)
│   └── db.php           # Connexion à la base de données (PDO)
│
├── includes/            # Fichiers réutilisables
│   ├── header.php       # En-tête HTML (DOCTYPE, head, début body)
│   ├── footer.php       # Pied de page (footer, scripts JS)
│   ├── nav.php          # Navigation principale (menu)
│   └── functions.php    # Fonctions PHP communes (panier, auth, etc.)
│
├── public/              # Pages accessibles publiquement
│   ├── index.php        # Page d'accueil
│   ├── boutique.php     # Liste de tous les produits
│   ├── produit.php      # Fiche détaillée d'un produit
│   ├── panier.php       # Gestion du panier (ajout, modification, suppression)
│   ├── commande.php     # Validation et enregistrement de la commande
│   ├── login.php        # Connexion utilisateur
│   ├── register.php     # Inscription nouveau client
│   ├── logout.php       # Déconnexion
│   ├── compte.php       # Espace client (profil, commandes)
│   ├── contact.php      # Formulaire de contact
│   │
│   ├── admin/           # Interface d'administration
│   │   ├── index.php    # Tableau de bord admin
│   │   └── products.php # Gestion des produits
│   │
│   └── assets/          # Ressources statiques
│       ├── css/
│       │   └── style.css    # Styles CSS du site
│       └── js/
│           └── main.js      # Scripts JavaScript (panier AJAX, validations)
│
└── database.sql         # Script SQL de création de la base de données
```

## 📄 Explication des fichiers

### Configuration (`config/`)

- **`config.php`** : Définit les constantes globales (URL de base, nom du site, etc.)
- **`db.php`** : Fonction `getPDO()` qui retourne une connexion PDO à la base de données (singleton)

### Fichiers réutilisables (`includes/`)

- **`header.php`** : Début de chaque page HTML (DOCTYPE, head avec CSS, ouverture body, navigation)
- **`footer.php`** : Fin de chaque page (footer, script JS, fermeture body/html)
- **`nav.php`** : Menu de navigation avec liens selon le rôle utilisateur
- **`functions.php`** : Fonctions utilitaires :
  - `redirect()` : Redirection vers une URL
  - `is_logged_in()` : Vérifie si l'utilisateur est connecté
  - `has_role()` : Vérifie le rôle de l'utilisateur
  - `get_cart()`, `add_to_cart()`, `update_cart()`, `clear_cart()` : Gestion du panier (session)

### Pages publiques (`public/`)

- **`index.php`** : Page d'accueil avec présentation du site et produits en vedette
- **`boutique.php`** : Affiche tous les produits avec filtres par catégorie
- **`produit.php`** : Fiche détaillée d'un produit (image, description, prix, ajout au panier)
- **`panier.php`** : Affiche le contenu du panier, permet de modifier les quantités ou vider le panier. Gère aussi les requêtes AJAX pour ajouter/modifier/supprimer des produits
- **`commande.php`** : Validation de la commande (récapitulatif, enregistrement en base)
- **`login.php`** : Formulaire de connexion (email + mot de passe)
- **`register.php`** : Formulaire d'inscription (nom, email, mot de passe)
- **`logout.php`** : Déconnecte l'utilisateur et redirige vers l'accueil
- **`compte.php`** : Espace client (informations personnelles, historique des commandes)
- **`contact.php`** : Formulaire de contact (enregistre les messages en base)

### Administration (`public/admin/`)

- **`index.php`** : Tableau de bord avec statistiques (nombre de produits, commandes, etc.)
- **`products.php`** : Gestion des produits (liste, ajout, modification, suppression)

### Assets (`public/assets/`)

- **`css/style.css`** : Tous les styles du site (responsive, thème vert/ferme)
- **`js/main.js`** : Scripts JavaScript pour :
  - Ajout au panier via AJAX
  - Validation des formulaires
  - Notifications utilisateur
  - Gestion des quantités

## 🗄️ Base de données

### Tables principales

1. **`users`** : Utilisateurs du site
   - `id`, `name`, `email`, `password_hash`, `role` (visiteur/client/admin), `phone`, `address`, `created_at`

2. **`categories`** : Catégories de produits
   - `id`, `name`, `description`, `image`, `created_at`

3. **`products`** : Produits en vente
   - `id`, `category_id`, `name`, `description`, `price`, `image`, `stock`, `unit`, `created_at`, `updated_at`

4. **`orders`** : Commandes
   - `id`, `user_id` (peut être NULL pour commande sans compte), `customer_name`, `customer_phone`, `customer_address`, `total_amount`, `status`, `created_at`, `updated_at`

5. **`order_items`** : Articles d'une commande
   - `id`, `order_id`, `product_id`, `quantity`, `unit_price`, `total_price`

6. **`contact_messages`** : Messages de contact
   - `id`, `name`, `email`, `message`, `read_at`, `created_at`

## 👥 Types d'utilisateurs

### Visiteur (non connecté)
- ✅ Parcourir les produits
- ✅ Voir les détails des produits
- ✅ Ajouter des produits au panier (stocké en session)
- ✅ Créer un compte
- ✅ Contacter la ferme
- ❌ Passer commande (doit être connecté)

### Client (connecté)
- ✅ Toutes les fonctionnalités du visiteur
- ✅ Passer commande
- ✅ Voir l'historique de ses commandes
- ✅ Accéder à son espace compte

### Administrateur
- ✅ Toutes les fonctionnalités du client
- ✅ Accéder à l'interface d'administration
- ✅ Gérer les produits (ajouter, modifier, supprimer)
- ✅ Gérer les catégories
- ✅ Voir toutes les commandes
- ✅ Gérer les utilisateurs

## 🎯 Parcours utilisateur

1. **Consultation** : Visiteur arrive sur la page d'accueil → clique sur "Boutique" → parcourt les produits
2. **Sélection** : Clique sur un produit → voit les détails → choisit la quantité → ajoute au panier
3. **Panier** : Consulte son panier → modifie les quantités si besoin → clique sur "Passer la commande"
4. **Authentification** : Si non connecté, redirigé vers la page de connexion/inscription
5. **Commande** : Remplit le formulaire de commande → valide → commande enregistrée en base

## 🛠️ Technologies utilisées

- **Frontend** : HTML5, CSS3 (responsive), JavaScript (vanilla, AJAX)
- **Backend** : PHP 7.4+ (procédural, adapté aux débutants)
- **Base de données** : MySQL 5.7+ / MariaDB
- **Serveur** : Apache (via WAMP/XAMPP)

## 📝 Notes importantes

- Ce projet est **pédagogique** : le code est simple et commenté pour faciliter l'apprentissage
- Les mots de passe sont hashés avec `password_hash()` (sécurité)
- Le panier est stocké en session PHP (temporaire, se vide à la fermeture du navigateur)
- Les images produits doivent être placées dans `public/assets/images/` (dossier à créer)
- Le site est responsive (s'adapte aux mobiles et tablettes)

## 🔒 Sécurité

- Protection contre les injections SQL (requêtes préparées avec PDO)
- Échappement des données utilisateur (`htmlspecialchars()`)
- Hashage des mots de passe
- Vérification des rôles pour l'accès admin
- Validation des formulaires côté serveur

## 📚 Objectifs pédagogiques

Ce projet permet d'apprendre :
- ✅ Structurer un projet web complet
- ✅ Utiliser PHP + MySQL pour gérer des données
- ✅ Comprendre le parcours utilisateur d'un site e‑commerce
- ✅ Gérer des rôles utilisateurs (visiteur, client, admin)
- ✅ Utiliser les sessions PHP
- ✅ Faire des requêtes AJAX
- ✅ Créer une interface responsive

---

**Ce projet ne copie pas le code d'un site existant mais s'inspire de sa structure et de son fonctionnement pour un usage pédagogique.**


