<?php
require_once __DIR__ . '/../../models/item.php';

$id   = (int)($_GET['id'] ?? 0);
$soin = $id > 0 ? item_get_by_id($pdo, $id) : false;

if (!$soin) {
    redirect('/admin/soins');
}

$erreurs = [];

$categories      = query_all($pdo, "SELECT id, nom FROM category ORDER BY nom");
$themes          = query_all($pdo, "SELECT id, nom FROM theme ORDER BY nom");
$tagsDisponibles = query_all($pdo, "SELECT id, nom FROM tag ORDER BY nom");

// Valeurs par défaut = valeurs actuelles en BDD
$valeurs = [
    'titre'             => $soin['titre'],
    'description_courte'=> $soin['description_courte'],
    'description'       => $soin['description'],
    'duree'             => (string)$soin['duree'],
    'prix'              => (string)$soin['prix'],
    'statut'            => $soin['statut'],
    'category_id'       => (string)$soin['category_id'],
    'theme_id'          => (string)$soin['theme_id'],
    'tags'              => array_column(query_all($pdo, "SELECT tag_id FROM item_tag WHERE item_id = :id", ['id' => $id]), 'tag_id'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs = [
        'titre'             => trim($_POST['titre'] ?? ''),
        'description_courte'=> trim($_POST['description_courte'] ?? ''),
        'description'       => trim($_POST['description'] ?? ''),
        'duree'             => trim($_POST['duree'] ?? ''),
        'prix'              => trim($_POST['prix'] ?? ''),
        'statut'            => $_POST['statut'] ?? 'brouillon',
        'category_id'       => $_POST['category_id'] ?? '',
        'theme_id'          => $_POST['theme_id'] ?? '',
        'tags'              => (array)($_POST['tags'] ?? []),
    ];

    if ($valeurs['titre'] === '')              $erreurs[] = "Le titre est obligatoire.";
    if ($valeurs['description_courte'] === '') $erreurs[] = "La description courte est obligatoire.";
    if ($valeurs['description'] === '')        $erreurs[] = "La description complète est obligatoire.";
    if (!is_numeric($valeurs['duree']) || (int)$valeurs['duree'] <= 0) $erreurs[] = "La durée doit être un nombre positif.";
    if (!is_numeric($valeurs['prix'])  || (float)$valeurs['prix'] <= 0) $erreurs[] = "Le prix doit être un nombre positif.";
    if ($valeurs['category_id'] === '')        $erreurs[] = "Veuillez choisir une catégorie.";
    if ($valeurs['theme_id'] === '')           $erreurs[] = "Veuillez choisir un thème.";
    if (!in_array($valeurs['statut'], ['brouillon', 'publie', 'archive'], true)) $erreurs[] = "Statut invalide.";

    // Le slug (adresse de la page) ne change que si le titre change
    $slug = $valeurs['titre'] === $soin['titre'] ? $soin['slug'] : item_slug($valeurs['titre']);

    // Unicité du slug (sauf pour ce même soin)
    if (empty($erreurs) && item_slug_exists($pdo, $slug, $id)) {
        $erreurs[] = "Un autre soin porte déjà ce nom (ou un nom très proche).";
    }

    if (empty($erreurs)) {
        $pdo->beginTransaction();

        item_update($pdo, [
            'id'                => $id,
            'titre'             => $valeurs['titre'],
            'slug'              => $slug,
            'description_courte'=> $valeurs['description_courte'],
            'description'       => $valeurs['description'],
            'duree'             => (int)$valeurs['duree'],
            'prix'              => (float)$valeurs['prix'],
            'statut'            => $valeurs['statut'],
            'theme_id'          => (int)$valeurs['theme_id'],
            'category_id'       => (int)$valeurs['category_id'],
        ]);
        item_tags_sync($pdo, $id, $valeurs['tags']);

        $pdo->commit();

        redirect('/admin/soins');
    }
}

echo render($base . '/views/soins-modifier.php', [
    'id'              => $id,
    'titreActuel'     => $soin['titre'],
    'erreurs'         => $erreurs,
    'valeurs'         => $valeurs,
    'categories'      => $categories,
    'themes'          => $themes,
    'tagsDisponibles' => $tagsDisponibles,
    'pageTitle'       => 'Modifier un soin – Admin',
]);
