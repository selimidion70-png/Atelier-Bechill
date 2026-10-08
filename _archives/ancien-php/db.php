<?php
/**
 * db.php (racine)
 * Connexion à la base de données pour le front public.
 * À inclure dans toutes les pages publiques qui ont besoin de la BDD :
 *   require_once __DIR__ . '/db.php';
 */

$host = 'localhost';
$dbname = 'bechill';
$user = 'root';
$password = ''; // mot de passe MySQL par défaut sur Laragon = vide

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
