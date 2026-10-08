<?php
// app/controllers/home.php
require_once __DIR__ . '/../models/item.php';
require_once __DIR__ . '/../models/collection.php';

echo render($base . '/views/home.php', [
    'soinsVedettes' => item_get_vedettes($pdo, 3),
    'collections'   => collection_get_publiques($pdo),
    'pageTitle'     => 'BE CHILL – Salon de massage à Bruxelles',
]);
