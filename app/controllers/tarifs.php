<?php
// app/controllers/tarifs.php
require_once __DIR__ . '/../models/item.php';

// Soins publiés, regroupés par catégorie
$soinsParCategorie = [];
foreach (item_get_all_published($pdo) as $soin) {
    $soinsParCategorie[$soin['categorie']][] = $soin;
}

echo render($base . '/views/tarifs.php', [
    'soinsParCategorie' => $soinsParCategorie,
    'pageTitle'         => 'Tarifs – BE CHILL',
]);
