<?php
// core/config.php
// Lecture des réglages : config/app.php, remplacé par config/app.local.php s'il existe

function config(string $cle): mixed
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config/app.php';
        $local  = __DIR__ . '/../config/app.local.php';
        if (file_exists($local)) {
            $config = array_replace_recursive($config, require $local);
        }
    }

    return $config[$cle] ?? null;
}

// Raccourci pour les coordonnées du salon : salon('telephone')
function salon(string $cle): string
{
    return config('salon')[$cle] ?? '';
}
