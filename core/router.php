<?php
// core/router.php
// Détermine quel controller appeler selon l'URL

// Détermine le nom du controller à partir des segments
// Ex: [] → 'home', ['soins'] → 'soins', ['soins', 'detail'] → 'soins'
function route(array $segments): string
{
    if (empty($segments)) {
        return 'home';
    }
    return $segments[0];
}

// Charge et exécute le controller, retourne le contenu HTML
function run(string $route, string $base, PDO $pdo): string
{
    $file = $base . '/controllers/' . $route . '.php';

    if (!file_exists($file)) {
        http_response_code(404);
        return '<main><h1>Page introuvable</h1><p><a href="/">Retour à l\'accueil</a></p></main>';
    }

    // Le controller doit retourner une string HTML via ob_start/ob_get_clean
    ob_start();
    require $file;
    return ob_get_clean();
}
