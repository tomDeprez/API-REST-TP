-- Création de la base de données
CREATE DATABASE IF NOT EXISTS nom_de_la_base;
USE nom_de_la_base;

-- Création de la table 'users'
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

-- Insertion de données exemple dans la table 'users'
INSERT INTO users (nom, email) VALUES ('Alice Dupont', 'alice@example.com');
INSERT INTO users (nom, email) VALUES ('Bob Martin', 'bob@example.com');

CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    categorie VARCHAR(100),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO produits (nom, description, prix, stock, categorie) VALUES ('Produit A', 'Description du produit A', 19.99, 100, 'Catégorie 1');
INSERT INTO produits (nom, description, prix, stock, categorie) VALUES ('Produit B', 'Description du produit B', 29.99, 50, 'Catégorie 2');
