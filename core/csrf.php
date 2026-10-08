<?php
// core/csrf.php
// Protection CSRF : chaque formulaire POST envoie un jeton secret lié à la session

// Retourne le jeton de la session (le crée si besoin)
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Champ caché à placer dans chaque <form method="post">
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

// Vérifie le jeton envoyé avec le formulaire
function csrf_check(): bool
{
    return is_string($_POST['csrf_token'] ?? null)
        && hash_equals(csrf_token(), $_POST['csrf_token']);
}
