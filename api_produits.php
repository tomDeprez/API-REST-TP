<?php
// Ce fichier pourrait être appelé api.php

// Active l'affichage des erreurs pour le débogage, à désactiver en production
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Connexion à la base de données
// Remplacez ces valeurs par vos informations de connexion
$host = 'localhost:3307';
$db   = 'testituser';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


// Analyse de la requête
$method = $_SERVER['REQUEST_METHOD'];
$pathInfo = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/') : '';
$request = explode('/', $pathInfo);

try {
    // Gestion des routes
    switch($method) {
        case 'GET':
            if (isset($request[0]) && $request[0] != '') {
                // Récupérer un produit spécifique par ID
                $stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
                $stmt->execute([$request[0]]);
                $produit = $stmt->fetch();
                echo json_encode($produit);
            } else {
                // Renvoyer tous les produits
                $stmt = $pdo->query('SELECT * FROM produits');
                $produits = $stmt->fetchAll();
                echo json_encode($produits);
            }
            break;

        case 'POST':
            // Ajouter un nouveau produit
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO produits (nom, description, prix, stock, categorie) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$data['nom'], $data['description'], $data['prix'], $data['stock'], $data['categorie']]);
            echo json_encode(['message' => 'Produit ajouté']);
            break;

        case 'PUT':
            // Mise à jour d'un produit
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, categorie = ? WHERE id = ?');
            $stmt->execute([$data['nom'], $data['description'], $data['prix'], $data['stock'], $data['categorie'], $request[0]]);
            echo json_encode(['message' => 'Produit mis à jour']);
            break;

        case 'DELETE':
            // Supprimer un produit
            $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
            $stmt->execute([$request[0]]);
            echo json_encode(['message' => 'Produit supprimé']);
            break;
    }
} catch (PDOException $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
