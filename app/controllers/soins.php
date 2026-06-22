<?php
// app/controllers/soins.php
require_once __DIR__ . '/../models/item.php';

$filtres = [
    'recherche' => trim($_GET['recherche'] ?? ''),
    'categorie' => $_GET['categorie'] ?? '',
    'theme'     => $_GET['theme'] ?? '',
    'duree'     => $_GET['duree'] ?? '',
];

$soins      = item_search($pdo, $filtres);
$categories = query_all($pdo, "SELECT id, nom FROM category ORDER BY nom");
$themes     = query_all($pdo, "SELECT id, nom FROM theme ORDER BY nom");

// Regroupement par catégorie
$soinsParCategorie = [];
foreach ($soins as $soin) {
    $soinsParCategorie[$soin['categorie']][] = $soin;
}

echo render($base . '/views/soins.php', [
    'soinsParCategorie' => $soinsParCategorie,
    'soins'             => $soins,
    'categories'        => $categories,
    'themes'            => $themes,
    'filtres'           => $filtres,
    'pageTitle'         => 'Nos soins – BE CHILL',
]);
