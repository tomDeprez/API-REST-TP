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


// Gestion des routes
switch($method) {
    case 'GET':
        if (isset($request[0]) && $request[0] != '') {
            // Récupérer un utilisateur spécifique par ID
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$request[0]]);
            $user = $stmt->fetch();
            echo json_encode($user);
        } else {
            // Bug introduit : au lieu de renvoyer tous les utilisateurs, renvoyer une erreur 404
            // http_response_code(404);
            // echo json_encode(['message' => 'Ressource non trouvée']);
            $stmt = $pdo->prepare('SELECT * FROM users');
            $stmt->execute();
            $users = $stmt->fetchAll();
            echo json_encode($users);
        }
        break;

    case 'POST':
        // Ajouter un utilisateur
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('INSERT INTO users (nom, email) VALUES (?, ?)');
        $stmt->execute([$data['nom'], $data['email']]);
        echo json_encode(['message' => 'Utilisateur créé']);
        break;

    case 'PUT':
        // Mise à jour d'un utilisateur
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('UPDATE users SET nom = ?, email = ? WHERE id = ?');
        $stmt->execute([$data['nom'], $data['email'], $request[0]]);
        echo json_encode(['message' => 'Utilisateur mis à jour']);
        break;

    case 'DELETE':
        // Supprimer un utilisateur
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$request[0]]);
        echo json_encode(['message' => 'Utilisateur supprimé']);
        break;
}

?>
