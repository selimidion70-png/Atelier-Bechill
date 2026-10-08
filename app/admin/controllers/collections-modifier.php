<?php
// Création (sans ?id) ou modification (?id=X) d'une collection
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../../models/collection.php';

$id         = (int)($_GET['id'] ?? 0);
$collection = $id > 0 ? collection_get($pdo, $id) : null;

if ($id > 0 && !$collection) {
    redirect('/admin/collections');
}

$erreurs = [];
$valeurs = [
    'nom'   => $collection['nom'] ?? '',
    'soins' => $collection ? collection_item_ids($pdo, $id) : [],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs = [
        'nom'   => trim($_POST['nom'] ?? ''),
        'soins' => array_map('intval', (array)($_POST['soins'] ?? [])),
    ];

    if ($valeurs['nom'] === '' || mb_strlen($valeurs['nom']) > 150) {
        $erreurs[] = "Le nom est obligatoire (150 caractères maximum).";
    } elseif (collection_nom_existe($pdo, $valeurs['nom'], $id)) {
        $erreurs[] = "Une collection porte déjà ce nom.";
    }
    if (empty($valeurs['soins'])) {
        $erreurs[] = "Choisissez au moins un soin.";
    }

    if (empty($erreurs)) {
        $pdo->beginTransaction();
        if ($collection) {
            collection_rename($pdo, $id, $valeurs['nom']);
        } else {
            $id = collection_insert($pdo, $valeurs['nom'], $_SESSION['operator_id']);
        }
        collection_items_sync($pdo, $id, $valeurs['soins']);
        $pdo->commit();

        redirect('/admin/collections');
    }
}

echo render($base . '/views/collections-modifier.php', [
    'id'        => $id,
    'creation'  => !$collection,
    'erreurs'   => $erreurs,
    'valeurs'   => $valeurs,
    'soins'     => item_get_all($pdo),
    'pageTitle' => ($collection ? 'Modifier' : 'Nouvelle') . ' collection – Admin',
]);
