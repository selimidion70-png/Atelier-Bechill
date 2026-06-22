<?php
// app/controllers/home.php
require_once __DIR__ . '/../models/item.php';

$soinsVedettes = item_get_vedettes($pdo, 3);

echo render($base . '/views/home.php', [
    'soinsVedettes' => $soinsVedettes,
    'pageTitle'     => 'BE CHILL – Salon de massage à Bruxelles',
]);
