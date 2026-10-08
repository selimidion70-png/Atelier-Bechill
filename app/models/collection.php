<?php
// app/models/collection.php
// Collections : sélections de soins créées dans l'admin (ex. « Spécial sportifs »)

function collection_get_all(PDO $pdo): array
{
    return query_all($pdo, "
        SELECT collection.id, collection.nom, collection.date_creation,
               operator.nom AS auteur,
               (SELECT COUNT(*) FROM collection_item WHERE collection_id = collection.id) AS nb_soins
        FROM collection
        JOIN operator ON collection.operator_id = operator.id
        ORDER BY collection.nom
    ");
}

function collection_get(PDO $pdo, int $id): array|false
{
    return query_one($pdo, "SELECT * FROM collection WHERE id = :id", ['id' => $id]);
}

function collection_item_ids(PDO $pdo, int $id): array
{
    return array_map('intval', array_column(query_all($pdo,
        "SELECT item_id FROM collection_item WHERE collection_id = :id", ['id' => $id]), 'item_id'));
}

function collection_nom_existe(PDO $pdo, string $nom, int $exclude_id = 0): bool
{
    return (bool)query_one($pdo, "SELECT id FROM collection WHERE nom = :nom AND id != :id", ['nom' => $nom, 'id' => $exclude_id]);
}

function collection_insert(PDO $pdo, string $nom, int $operator_id): int
{
    query_run($pdo, "INSERT INTO collection (nom, operator_id) VALUES (:nom, :operator_id)", [
        'nom'         => $nom,
        'operator_id' => $operator_id,
    ]);
    return (int)query_last_id($pdo);
}

function collection_rename(PDO $pdo, int $id, string $nom): void
{
    query_run($pdo, "UPDATE collection SET nom = :nom WHERE id = :id", ['nom' => $nom, 'id' => $id]);
}

function collection_items_sync(PDO $pdo, int $id, array $item_ids): void
{
    query_run($pdo, "DELETE FROM collection_item WHERE collection_id = :id", ['id' => $id]);
    foreach ($item_ids as $item_id) {
        query_run($pdo, "INSERT INTO collection_item (collection_id, item_id) VALUES (:collection_id, :item_id)", [
            'collection_id' => $id,
            'item_id'       => (int)$item_id,
        ]);
    }
}

function collection_delete(PDO $pdo, int $id): void
{
    query_run($pdo, "DELETE FROM collection WHERE id = :id", ['id' => $id]);
}

// Pour le site public : collections avec leurs soins publiés (collections vides ignorées)
function collection_get_publiques(PDO $pdo): array
{
    $lignes = query_all($pdo, "
        SELECT collection.id AS collection_id, collection.nom AS collection,
               item.id, item.titre, item.slug, item.duree, item.prix
        FROM collection
        JOIN collection_item ON collection_item.collection_id = collection.id
        JOIN item ON item.id = collection_item.item_id
        WHERE item.statut = 'publie'
        ORDER BY collection.nom, item.titre
    ");

    $collections = [];
    foreach ($lignes as $l) {
        $collections[$l['collection_id']]['nom']     = $l['collection'];
        $collections[$l['collection_id']]['soins'][] = $l;
    }
    return $collections;
}
