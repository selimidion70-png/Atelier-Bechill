<?php
// app/controllers/soin-detail.php
require_once __DIR__ . '/../models/item.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
    redirect('/soins');
}

$soin = item_get_by_slug($pdo, $slug);

if (!$soin) {
    redirect('/soins');
}

$tags = item_get_tags($pdo, $soin['id']);

echo render($base . '/views/soin-detail.php', [
    'soin'      => $soin,
    'tags'      => $tags,
    'pageTitle' => $soin['titre'] . ' – BE CHILL',
]);
