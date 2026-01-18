-- Base de données pour La Ferme - Site e-commerce pédagogique
-- Créer d'abord la base de données : CREATE DATABASE la_ferme;

USE la_ferme;

-- Table des utilisateurs (visiteurs, clients, administrateurs)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('visiteur', 'client', 'admin') DEFAULT 'client',
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des produits
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    unit VARCHAR(50) DEFAULT 'unité',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_name (name),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    customer_address TEXT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('en_attente', 'en_cours', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des articles de commande
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des messages de contact
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--table orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    status VARCHAR(50),
    created_at DATETIME DEFAULT 
);

--table orders_items
CREATE TABLE orders_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insertion des catégories
INSERT INTO categories (name, description, image) VALUES
('Poussins & volailles', 'Poussins, poulets, canards et autres volailles de qualité', 'category_poussins.jpg'),
('Aliments avicoles', 'Aliments complets et complémentaires pour vos volailles', 'category_aliments.jpg'),
('Matériel avicole', 'Équipements et accessoires pour l’élevage avicole', 'category_materiel.jpg'),
('Œufs & produits frais', 'Œufs frais et produits dérivés de la ferme', 'category_oeufs.jpg');

-- Insertion des produits
INSERT INTO products (category_id, name, description, price, image, stock, unit) VALUES
-- Poussins & volailles
(1, 'Poussins de chair', 'Poussins de race à croissance rapide, idéaux pour la production de viande', 1500.00, 'poussin_chair.jpg', 100, 'carton de 50'),
(1, 'Poussins pondeuses', 'Poussins de race pondeuse, excellente production d’œufs', 1800.00, 'poussin_pondeuse.jpg', 80, 'carton de 50'),
(1, 'Poussin Bleu d’holland', 'Poussins de race hybridre, excellente production d’œufs et de viande', 1800.00, 'poussin_bleu_holland.jpg', 80, 'carton de 50'),
(1, 'Poussin gouliate', 'Poussins de race hybridre, excellente production d’œufs et de viande', 1800.00, 'poussin_gouliate.jpg', 80, 'carton de 50'),
(1, 'Poulets fermiers', 'Poulets fermiers élevés en plein air, prêts à consommer', 3500.00, 'poulet_fermier.jpg', 50, 'unité'),
(1, 'Canards de Barbarie', 'Canards de race Barbarie, robustes et adaptés au climat local', 2500.00, 'canard_barbarie.jpg', 30, 'unité'),

-- Aliments avicoles
(2, 'Aliment démarrage poussins', 'Aliment complet pour poussins de 0 à 3 semaines, riche en protéines', 18000.00, 'aliment_demarrage.jpg', 200, 'sac 50kg'),
(2, 'Aliment croissance poulets', 'Aliment pour poulets de 3 à 8 semaines, équilibré en nutriments', 18000.00, 'aliment_croissance.jpg', 350, 'sac 50kg'),
(2, 'Aliment ponte', 'Aliment spécialisé pour poules pondeuses, riche en calcium', 17000.00, 'aliment_ponte.jpg', 180, 'sac 50kg'),
(2, 'Maïs concassé', 'Maïs concassé de qualité pour complément alimentaire', 11000.00, 'mais_concasse.jpg', 300, 'sac 50kg'),

-- Matériel avicole
(3, 'Mangeoire automatique', 'Mangeoire automatique en plastique, capacité 5kg', 4500.00, 'mangeoire_auto.jpg', 40, 'unité'),
(3, 'Abreuvoir à tétine', 'Abreuvoir automatique avec tétines, hygiénique et pratique', 3500.00, 'abreuvoir_tetine.jpg', 50, 'unité'),
(3, 'Pondeuse en plastique', 'Pondeuse individuelle pour la collecte des œufs', 1200.00, 'pondeuse.jpg', 100, 'unité'),
(3, 'Grillage avicole', 'Grillage galvanisé pour clôture de parcours, rouleau 50m', 15000.00, 'grillage.jpg', 20, 'rouleau'),

-- Œufs & produits frais
(4, 'Œufs frais fermiers', 'Œufs frais de poules élevées en plein air, boîte de 30', 2500.00, 'oeufs_frais.jpg', 200, 'boîte 30'),
(4, 'Œufs de canard', 'Œufs frais de canard, plus gros et nutritifs', 3500.00, 'oeufs_canard.jpg', 80, 'boîte 12'),
(4, 'Œufs de caille', 'Œufs de caille frais, délicieux et nutritifs', 2000.00, 'oeufs_caille.jpg', 150, 'boîte 30'),
(4, 'Poulet préparé', 'Poulet fermier préparé et prêt à cuisiner', 4000.00, 'poulet_prepare.jpg', 30, 'unité');

-- Insertion d'un utilisateur administrateur par défaut
-- Email: admin@laferme.com
-- Mot de passe: admin123
-- (Le hash a été généré avec password_hash('admin123', PASSWORD_DEFAULT))
INSERT INTO users (name, email, password_hash, role) VALUES
('Administrateur', 'admin@laferme.com', '$2y$10$ET/RvT3MWMORUIUSeHqqdupTUvt0KUUWIbYe7pDbL3//EAIzksrq2', 'admin');

