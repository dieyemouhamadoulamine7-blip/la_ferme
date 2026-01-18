-- Migration : Ajout du champ payment_method à la table orders
-- À exécuter si le champ n'existe pas déjà

USE la_ferme;

ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT NULL 
AFTER customer_address;

-- Mettre à jour les valeurs possibles si nécessaire
-- Les valeurs possibles sont : 'orange_money', 'wave', 'livraison'
