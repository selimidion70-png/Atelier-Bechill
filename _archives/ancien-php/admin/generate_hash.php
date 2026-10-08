<?php
/**
 * generate_hash.php
 *
 * À LANCER UNE SEULE FOIS sur ton serveur PHP (Laragon)
 * pour générer un hash de mot de passe compatible avec password_verify().
 *
 * Usage :
 * 1. Place ce fichier dans le dossier de ton projet (ex: /admin/generate_hash.php)
 * 2. Ouvre-le dans le navigateur : http://localhost/admin/generate_hash.php
 * 3. Copie le hash affiché
 * 4. Remplace le hash de l'INSERT INTO operator dans schema.sql par celui-ci
 *    (ou fais directement un UPDATE en base)
 * 5. SUPPRIME ce fichier une fois terminé (ne jamais le laisser en prod)
 */

$motDePasse = 'admin123';
$hash = password_hash($motDePasse, PASSWORD_DEFAULT);

echo "Mot de passe en clair : " . $motDePasse . "<br>";
echo "Hash à utiliser : " . $hash . "<br><br>";
echo "Requête SQL à exécuter :<br>";
echo "<pre>UPDATE operator SET mot_de_passe = '" . $hash . "' WHERE email = 'admin@bechill.be';</pre>";
