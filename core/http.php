<?php
// core/http.php
// Fonctions utilitaires HTTP

// Lit l'URL et retourne un tableau de segments
// Ex: /soins/detail → ['soins', 'detail']
function http_in(string $uri): array
{
    // On retire le préfixe du projet (ex: /Atelier-Bechill)
    $uri = preg_replace('#^/Atelier-Bechill#', '', $uri);

    // On retire les paramètres GET (?slug=...)
    $uri = strtok($uri, '?');

    // On découpe par /
    $segments = explode('/', trim($uri, '/'));

    // On filtre les segments vides
    return array_values(array_filter($segments, fn($s) => $s !== ''));
}

// Envoie la réponse HTTP
function http_out(int $code, string $body): void
{
    http_response_code($code);
    echo $body;
}

// Redirige vers une URL
function redirect(string $url): void
{
    header('Location: /Atelier-Bechill' . $url);
    exit;
}
