<?php
require_once __DIR__ . '/../../models/item.php';

$erreurs = [];
$succes  = false;
$valeurs = [
    'titre'             => '',
    'description_courte'=> '',
    'description'       => '',
    'duree'             => '',
    'prix'              => '',
    'statut'            => 'brouillon',
    'category_id'       => '',
    'theme_id'          => '',
];

$categories = query_all($pdo, "SELECT id, nom FROM category ORDER BY nom");
$themes     = query_all($pdo, "SELECT id, nom FROM theme ORDER BY nom");

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
    ];

    if ($valeurs['titre'] === '')              $erreurs[] = "Le titre est obligatoire.";
    if ($valeurs['description_courte'] === '') $erreurs[] = "La description courte est obligatoire.";
    if ($valeurs['description'] === '')        $erreurs[] = "La description complète est obligatoire.";
    if (!is_numeric($valeurs['duree']) || (int)$valeurs['duree'] <= 0) $erreurs[] = "La durée doit être un nombre positif.";
    if (!is_numeric($valeurs['prix'])  || (float)$valeurs['prix'] <= 0) $erreurs[] = "Le prix doit être un nombre positif.";
    if ($valeurs['category_id'] === '')        $erreurs[] = "Veuillez choisir une catégorie.";
    if ($valeurs['theme_id'] === '')           $erreurs[] = "Veuillez choisir un thème.";

    if (empty($erreurs)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $valeurs['titre'])));

        item_insert($pdo, [
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

        $succes = true;
        $valeurs = array_fill_keys(array_keys($valeurs), '');
        $valeurs['statut'] = 'brouillon';
    }
}

echo render($base . '/views/soins-ajouter.php', [
    'erreurs'    => $erreurs,
    'succes'     => $succes,
    'valeurs'    => $valeurs,
    'categories' => $categories,
    'themes'     => $themes,
    'pageTitle'  => 'Ajouter un soin – Admin',
]);
