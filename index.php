<?php
// index.php
// Point d'entrée unique — toutes les requêtes passent par ici

require 'core/config.php';

// En production : erreurs PHP cachées aux visiteurs (elles vont dans le journal du serveur)
$production = config('environnement') === 'production';
ini_set('display_errors', $production ? '0' : '1');
error_reporting(E_ALL);

// Cookie de session : inaccessible au JavaScript, non envoyé depuis d'autres sites, HTTPS en production
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => $production,
]);
session_start();

require 'core/http.php';
require 'core/router.php';
require 'core/html.php';
require 'core/query.php';
require 'core/csrf.php';
require 'core/mail.php';

require 'config/database.php';

$base = __DIR__ . '/app';

// Lecture de l'URL
$segments = http_in($_SERVER['REQUEST_URI']);

// Si l'URL commence par "admin"
$estAdmin = isset($segments[0]) && $segments[0] === 'admin';
if ($estAdmin) {
    require 'app/admin/acces.php';

    // Le compte est revérifié à chaque page : un compte désactivé est déconnecté aussitôt
    if (!empty($_SESSION['operator_id'])) {
        $operator = query_one($pdo, "SELECT role, actif FROM operator WHERE id = :id", ['id' => $_SESSION['operator_id']]);
        if (!$operator || !$operator['actif'] || !isset(ADMIN_ROLES[$operator['role']])) {
            session_destroy();
            redirect('/admin/login');
        }
        $_SESSION['operator_role'] = $operator['role'];
    }

    // Vérification de la session (sauf pour login)
    if (empty($_SESSION['operator_id']) && ($segments[1] ?? '') !== 'login') {
        redirect('/admin/login');
    }

    $base = __DIR__ . '/app/admin';
    array_shift($segments); // On retire "admin" des segments
}

// Détermination de la route
$route = route($segments);

// Exécution du controller (tout formulaire POST doit avoir un jeton CSRF valide)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_check()) {
    http_response_code(403);
    $main = '<main><h1>Formulaire expiré</h1><p>Veuillez recharger la page et renvoyer le formulaire.</p></main>';
} elseif ($estAdmin && !empty($_SESSION['operator_id']) && !admin_peut($_SESSION['operator_role'], $route)) {
    http_response_code(403);
    $main = '<main><h1>Accès refusé</h1><p>Cette page est réservée à l\'administrateur. <a href="/admin">Retour au tableau de bord</a></p></main>';
} else {
    $main = run($route, $base, $pdo);
}

// Rendu dans le layout
$body = render($base . '/views/_layout.php', [
    'page_content' => $main,
]);

// Envoi de la réponse
// (garde le code 404 éventuellement défini par run())
http_out(http_response_code() ?: 200, $body);
