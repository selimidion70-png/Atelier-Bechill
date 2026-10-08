<?php
// index.php
// Point d'entrée unique — toutes les requêtes passent par ici

session_start();

require 'core/http.php';
require 'core/router.php';
require 'core/html.php';
require 'core/query.php';

require 'config/database.php';

$base = __DIR__ . '/app';

// Lecture de l'URL
$segments = http_in($_SERVER['REQUEST_URI']);

// Si l'URL commence par "admin"
if (isset($segments[0]) && $segments[0] === 'admin') {

    // Vérification de la session (sauf pour login)
    if (empty($_SESSION['operator_id']) && ($segments[1] ?? '') !== 'login') {
        redirect('/admin/login');
    }

    $base = __DIR__ . '/app/admin';
    array_shift($segments); // On retire "admin" des segments
}

// Détermination de la route
$route = route($segments);

// Exécution du controller
$main = run($route, $base, $pdo);

// Rendu dans le layout
$body = render($base . '/views/_layout.php', [
    'page_content' => $main,
]);

// Envoi de la réponse
// (garde le code 404 éventuellement défini par run())
http_out(http_response_code() ?: 200, $body);
