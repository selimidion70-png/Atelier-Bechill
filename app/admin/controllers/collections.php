<?php
require_once __DIR__ . '/../../models/collection.php';

// Supprimer une collection (les soins eux-mêmes ne sont pas touchés)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    collection_delete($pdo, (int)$_POST['supprimer_id']);
    redirect('/admin/collections');
}

echo render($base . '/views/collections.php', [
    'collections' => collection_get_all($pdo),
    'pageTitle'   => 'Collections – Admin',
]);
