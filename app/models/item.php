<?php
// app/models/item.php
// Toutes les requêtes SQL liées aux items (soins)

function item_get_all_published(PDO $pdo): array
{
    return query_all($pdo, "
        SELECT item.id, item.titre, item.slug, item.description_courte, item.image,
               item.duree, item.prix,
               category.nom AS categorie,
               theme.nom AS theme
        FROM item
        JOIN category ON item.category_id = category.id
        JOIN theme ON item.theme_id = theme.id
        WHERE item.statut = 'publie'
        ORDER BY category.nom, item.titre
    ");
}

function item_get_by_slug(PDO $pdo, string $slug): array|false
{
    return query_one($pdo, "
        SELECT item.*, category.nom AS categorie, theme.nom AS theme
        FROM item
        JOIN category ON item.category_id = category.id
        JOIN theme ON item.theme_id = theme.id
        WHERE item.slug = :slug AND item.statut = 'publie'
    ", ['slug' => $slug]);
}

function item_get_vedettes(PDO $pdo, int $limit = 3): array
{
    return query_all($pdo, "
        SELECT id, titre, slug, description_courte, image
        FROM item
        WHERE statut = 'publie'
        ORDER BY date_creation DESC
        LIMIT $limit
    ");
}

function item_search(PDO $pdo, array $filtres): array
{
    $sql = "
        SELECT item.id, item.titre, item.slug, item.description_courte, item.image,
               item.duree, item.prix,
               category.id AS category_id, category.nom AS categorie,
               theme.id AS theme_id, theme.nom AS theme
        FROM item
        JOIN category ON item.category_id = category.id
        JOIN theme ON item.theme_id = theme.id
        WHERE item.statut = 'publie'
    ";
    $params = [];

    if (!empty($filtres['recherche'])) {
        $like = '%' . $filtres['recherche'] . '%';
        $sql .= " AND (item.titre LIKE :r1 OR item.description_courte LIKE :r2 OR item.description LIKE :r3
                  OR item.id IN (SELECT item_tag.item_id FROM item_tag JOIN tag ON item_tag.tag_id = tag.id WHERE tag.nom LIKE :r4))";
        $params['r1'] = $like;
        $params['r2'] = $like;
        $params['r3'] = $like;
        $params['r4'] = $like;
    }
    if (!empty($filtres['categorie'])) {
        $sql .= " AND category.id = :categorie_id";
        $params['categorie_id'] = $filtres['categorie'];
    }
    if (!empty($filtres['theme'])) {
        $sql .= " AND theme.id = :theme_id";
        $params['theme_id'] = $filtres['theme'];
    }
    if (!empty($filtres['duree']) && is_numeric($filtres['duree'])) {
        $sql .= " AND item.duree <= :duree";
        $params['duree'] = (int)$filtres['duree'];
    }

    $sql .= " ORDER BY category.nom, item.titre";

    return query_all($pdo, $sql, $params);
}

function item_get_tags(PDO $pdo, int $item_id): array
{
    return array_column(query_all($pdo, "
        SELECT tag.nom FROM tag
        JOIN item_tag ON tag.id = item_tag.tag_id
        WHERE item_tag.item_id = :id
        ORDER BY tag.nom
    ", ['id' => $item_id]), 'nom');
}

function item_get_all(PDO $pdo): array
{
    return query_all($pdo, "
        SELECT item.id, item.titre, item.image, item.duree, item.prix, item.statut,
               category.nom AS categorie
        FROM item
        JOIN category ON item.category_id = category.id
        ORDER BY item.titre
    ");
}

// Génère le slug à partir du titre. Ex: "Massage été" → "massage-ete"
function item_slug(string $titre): string
{
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $titre);
    // Sous Windows, iconv transforme "é" en "'e" : on retire ces accents résiduels
    $slug = str_replace(["'", '`', '^', '"', '~'], '', $slug);
    return trim(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $slug)), '-');
}

// Vérifie si un slug est déjà utilisé par un autre soin (colonne UNIQUE en BDD)
function item_slug_exists(PDO $pdo, string $slug, int $exclude_id = 0): bool
{
    return (bool)query_one($pdo, "SELECT id FROM item WHERE slug = :slug AND id != :id", [
        'slug' => $slug,
        'id'   => $exclude_id,
    ]);
}

const ITEM_STATUTS = ['brouillon', 'publie', 'archive'];

// Lit les champs du formulaire « soin » (ajout / modification)
function item_form_lire(array $post): array
{
    return [
        'titre'             => trim($post['titre'] ?? ''),
        'description_courte'=> trim($post['description_courte'] ?? ''),
        'description'       => trim($post['description'] ?? ''),
        'duree'             => trim($post['duree'] ?? ''),
        'prix'              => trim($post['prix'] ?? ''),
        'statut'            => $post['statut'] ?? 'brouillon',
        'category_id'       => $post['category_id'] ?? '',
        'theme_id'          => $post['theme_id'] ?? '',
        'tags'              => array_map('intval', (array)($post['tags'] ?? [])),
    ];
}

// Vérifie les champs du formulaire « soin », retourne la liste des erreurs
function item_form_erreurs(array $v): array
{
    $erreurs = [];
    if ($v['titre'] === '')              $erreurs[] = "Le titre est obligatoire.";
    if ($v['description_courte'] === '') $erreurs[] = "La description courte est obligatoire.";
    if ($v['description'] === '')        $erreurs[] = "La description complète est obligatoire.";
    if (!is_numeric($v['duree']) || (int)$v['duree'] <= 0) $erreurs[] = "La durée doit être un nombre positif.";
    if (!is_numeric($v['prix'])  || (float)$v['prix'] <= 0) $erreurs[] = "Le prix doit être un nombre positif.";
    if ($v['category_id'] === '')        $erreurs[] = "Veuillez choisir une catégorie.";
    if ($v['theme_id'] === '')           $erreurs[] = "Veuillez choisir un thème.";
    if (!in_array($v['statut'], ITEM_STATUTS, true)) $erreurs[] = "Statut invalide.";
    return $erreurs;
}

function item_get_by_id(PDO $pdo, int $id): array|false
{
    return query_one($pdo, "SELECT * FROM item WHERE id = :id", ['id' => $id]);
}

function item_insert(PDO $pdo, array $data): int
{
    query_run($pdo, "
        INSERT INTO item (titre, slug, description_courte, description, image, duree, prix, statut, operator_id, theme_id, category_id)
        VALUES (:titre, :slug, :description_courte, :description, :image, :duree, :prix, :statut, :operator_id, :theme_id, :category_id)
    ", $data);
    return (int)query_last_id($pdo);
}

function item_update(PDO $pdo, array $data): void
{
    query_run($pdo, "
        UPDATE item SET
            titre = :titre, slug = :slug,
            description_courte = :description_courte, description = :description, image = :image,
            duree = :duree, prix = :prix, statut = :statut,
            theme_id = :theme_id, category_id = :category_id
        WHERE id = :id
    ", $data);
}

function item_delete(PDO $pdo, int $id): void
{
    $soin = item_get_by_id($pdo, $id);
    query_run($pdo, "DELETE FROM item WHERE id = :id", ['id' => $id]);
    if ($soin) {
        item_image_supprimer($soin['image']);
    }
}

// ---- Images des soins (dossier uploads/soins/) ----

const ITEM_IMAGE_DOSSIER = __DIR__ . '/../../uploads/soins/';
const ITEM_IMAGE_TYPES   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
const ITEM_IMAGE_MAX     = 3 * 1024 * 1024; // 3 Mo

// URL publique d'une image (ou null si le soin n'en a pas)
function item_image_url(?string $image): ?string
{
    return $image ? '/uploads/soins/' . rawurlencode($image) : null;
}

// Enregistre l'image envoyée par le formulaire.
// Retourne [nom du fichier, null] si OK, [null, null] si aucun fichier, [null, erreur] sinon.
function item_image_enregistrer(?array $fichier): array
{
    if (!$fichier || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        return [null, "L'envoi de l'image a échoué. Réessayez."];
    }
    if ($fichier['size'] > ITEM_IMAGE_MAX) {
        return [null, "L'image est trop lourde (3 Mo maximum)."];
    }

    // On vérifie le vrai contenu du fichier, pas seulement son extension
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']);
    if (!isset(ITEM_IMAGE_TYPES[$type]) || !getimagesize($fichier['tmp_name'])) {
        return [null, "L'image doit être au format JPG, PNG ou WebP."];
    }

    // Nom aléatoire : impossible de deviner ou d'écraser un autre fichier
    $nom = bin2hex(random_bytes(12)) . '.' . ITEM_IMAGE_TYPES[$type];
    if (!move_uploaded_file($fichier['tmp_name'], ITEM_IMAGE_DOSSIER . $nom)) {
        return [null, "Impossible d'enregistrer l'image sur le serveur."];
    }
    return [$nom, null];
}

function item_image_supprimer(?string $image): void
{
    // basename() empêche de sortir du dossier uploads/soins/
    if ($image && is_file(ITEM_IMAGE_DOSSIER . basename($image))) {
        unlink(ITEM_IMAGE_DOSSIER . basename($image));
    }
}

function item_set_statut(PDO $pdo, int $id, string $statut): void
{
    query_run($pdo, "UPDATE item SET statut = :statut WHERE id = :id", ['statut' => $statut, 'id' => $id]);
}

function item_tags_sync(PDO $pdo, int $item_id, array $tag_ids): void
{
    query_run($pdo, "DELETE FROM item_tag WHERE item_id = :id", ['id' => $item_id]);
    foreach ($tag_ids as $tag_id) {
        query_run($pdo, "INSERT INTO item_tag (item_id, tag_id) VALUES (:item_id, :tag_id)", [
            'item_id' => $item_id,
            'tag_id'  => (int)$tag_id,
        ]);
    }
}
