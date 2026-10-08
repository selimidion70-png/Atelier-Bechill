<?php
// app/models/item.php
// Toutes les requêtes SQL liées aux items (soins)

function item_get_all_published(PDO $pdo): array
{
    return query_all($pdo, "
        SELECT item.id, item.titre, item.slug, item.description_courte,
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
        SELECT titre, slug, description_courte
        FROM item
        WHERE statut = 'publie'
        ORDER BY date_creation DESC
        LIMIT $limit
    ");
}

function item_search(PDO $pdo, array $filtres): array
{
    $sql = "
        SELECT item.id, item.titre, item.slug, item.description_courte,
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
        SELECT item.id, item.titre, item.duree, item.prix, item.statut,
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

function item_get_by_id(PDO $pdo, int $id): array|false
{
    return query_one($pdo, "SELECT * FROM item WHERE id = :id", ['id' => $id]);
}

function item_insert(PDO $pdo, array $data): int
{
    query_run($pdo, "
        INSERT INTO item (titre, slug, description_courte, description, duree, prix, statut, operator_id, theme_id, category_id)
        VALUES (:titre, :slug, :description_courte, :description, :duree, :prix, :statut, :operator_id, :theme_id, :category_id)
    ", $data);
    return (int)query_last_id($pdo);
}

function item_update(PDO $pdo, array $data): void
{
    query_run($pdo, "
        UPDATE item SET
            titre = :titre, slug = :slug,
            description_courte = :description_courte, description = :description,
            duree = :duree, prix = :prix, statut = :statut,
            theme_id = :theme_id, category_id = :category_id
        WHERE id = :id
    ", $data);
}

function item_delete(PDO $pdo, int $id): void
{
    query_run($pdo, "DELETE FROM item WHERE id = :id", ['id' => $id]);
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
