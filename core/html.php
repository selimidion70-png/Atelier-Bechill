<?php
// core/html.php
// Fonction render : charge une vue et injecte des variables

function render(string $viewPath, array $data = []): string
{
    // On extrait les variables pour les rendre disponibles dans la vue
    extract($data);

    ob_start();
    require $viewPath;
    return ob_get_clean();
}
