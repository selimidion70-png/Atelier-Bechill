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
    $valeurs = item_form_lire($_POST);
    $erreurs = item_form_erreurs($valeurs);

    // Le slug (adresse de la page) ne change que si le titre change
    $slug = $valeurs['titre'] === $soin['titre'] ? $soin['slug'] : item_slug($valeurs['titre']);

    // Unicité du slug (sauf pour ce même soin)
    if (empty($erreurs) && item_slug_exists($pdo, $slug, $id)) {
        $erreurs[] = "Un autre soin porte déjà ce nom (ou un nom très proche).";
    }

    // Image : on garde l'actuelle, sauf si une nouvelle est envoyée ou si on coche « supprimer »
    $image         = $soin['image'];
    $nouvelleImage = null;
    if (empty($erreurs)) {
        [$nouvelleImage, $erreurImage] = item_image_enregistrer($_FILES['image'] ?? null);
        if ($erreurImage) {
            $erreurs[] = $erreurImage;
        } elseif ($nouvelleImage) {
            $image = $nouvelleImage;
        } elseif (isset($_POST['supprimer_image'])) {
            $image = null;
        }
    }

    if (empty($erreurs)) {
        $pdo->beginTransaction();

        item_update($pdo, [
            'id'                => $id,
            'titre'             => $valeurs['titre'],
            'slug'              => $slug,
            'description_courte'=> $valeurs['description_courte'],
            'description'       => $valeurs['description'],
            'image'             => $image,
            'duree'             => (int)$valeurs['duree'],
            'prix'              => (float)$valeurs['prix'],
            'statut'            => $valeurs['statut'],
            'theme_id'          => (int)$valeurs['theme_id'],
            'category_id'       => (int)$valeurs['category_id'],
        ]);
        item_tags_sync($pdo, $id, $valeurs['tags']);

        $pdo->commit();

        // L'ancien fichier n'est supprimé qu'une fois la base à jour
        if ($image !== $soin['image']) {
            item_image_supprimer($soin['image']);
        }

        redirect('/admin/soins');
    }
}

echo render($base . '/views/soins-modifier.php', [
    'id'              => $id,
    'titreActuel'     => $soin['titre'],
    'imageActuelle'   => $soin['image'],
    'erreurs'         => $erreurs,
    'valeurs'         => $valeurs,
    'categories'      => $categories,
    'themes'          => $themes,
    'tagsDisponibles' => $tagsDisponibles,
    'pageTitle'       => 'Modifier un soin – Admin',
]);
