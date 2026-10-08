<?php
require_once __DIR__ . '/../../models/item.php';

$erreurs = [];
$succes  = false;
$valeurs = item_form_lire([]);

$categories      = query_all($pdo, "SELECT id, nom FROM category ORDER BY nom");
$themes          = query_all($pdo, "SELECT id, nom FROM theme ORDER BY nom");
$tagsDisponibles = query_all($pdo, "SELECT id, nom FROM tag ORDER BY nom");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs = item_form_lire($_POST);
    $erreurs = item_form_erreurs($valeurs);

    $slug = item_slug($valeurs['titre']);
    if (empty($erreurs) && item_slug_exists($pdo, $slug)) {
        $erreurs[] = "Un soin avec ce nom (ou un nom très proche) existe déjà.";
    }

    if (empty($erreurs)) {
        $pdo->beginTransaction();

        $id = item_insert($pdo, [
            'titre'             => $valeurs['titre'],
            'slug'              => $slug,
            'description_courte'=> $valeurs['description_courte'],
            'description'       => $valeurs['description'],
            'duree'             => (int)$valeurs['duree'],
            'prix'              => (float)$valeurs['prix'],
            'statut'            => $valeurs['statut'],
            'operator_id'       => $_SESSION['operator_id'],
            'theme_id'          => (int)$valeurs['theme_id'],
            'category_id'       => (int)$valeurs['category_id'],
        ]);
        item_tags_sync($pdo, $id, $valeurs['tags']);

        $pdo->commit();

        $succes  = true;
        $valeurs = item_form_lire([]);
    }
}

echo render($base . '/views/soins-ajouter.php', [
    'erreurs'         => $erreurs,
    'succes'          => $succes,
    'valeurs'         => $valeurs,
    'categories'      => $categories,
    'themes'          => $themes,
    'tagsDisponibles' => $tagsDisponibles,
    'pageTitle'       => 'Ajouter un soin – Admin',
]);
