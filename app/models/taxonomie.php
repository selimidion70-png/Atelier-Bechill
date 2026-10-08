<?php
// app/models/taxonomie.php
// Catégories, thèmes et tags : même structure (id, nom), même gestion.
// Les noms de tables viennent uniquement de cette liste, jamais de l'utilisateur.

const TAXONOMIES = [
    'categories' => [
        'table'     => 'category',
        'titre'     => 'Catégories',
        'singulier' => 'catégorie',
        // Une catégorie utilisée par un soin ne peut pas être supprimée (clé étrangère)
        'compte'    => 'SELECT COUNT(*) FROM item WHERE category_id = t.id',
        'bloquante' => true,
    ],
    'themes' => [
        'table'     => 'theme',
        'titre'     => 'Thèmes',
        'singulier' => 'thème',
        'compte'    => 'SELECT COUNT(*) FROM item WHERE theme_id = t.id',
        'bloquante' => true,
    ],
    'tags' => [
        'table'     => 'tag',
        'titre'     => 'Tags',
        'singulier' => 'tag',
        // Supprimer un tag le retire simplement des soins (ON DELETE CASCADE)
        'compte'    => 'SELECT COUNT(*) FROM item_tag WHERE tag_id = t.id',
        'bloquante' => false,
    ],
];

// Liste avec le nombre de soins qui utilisent chaque élément
function taxonomie_get_all(PDO $pdo, array $tx): array
{
    return query_all($pdo, "SELECT t.id, t.nom, ({$tx['compte']}) AS nb_soins FROM {$tx['table']} t ORDER BY t.nom");
}

function taxonomie_get(PDO $pdo, array $tx, int $id): array|false
{
    return query_one($pdo, "SELECT t.id, t.nom, ({$tx['compte']}) AS nb_soins FROM {$tx['table']} t WHERE t.id = :id", ['id' => $id]);
}

function taxonomie_nom_existe(PDO $pdo, array $tx, string $nom, int $exclude_id = 0): bool
{
    return (bool)query_one($pdo, "SELECT id FROM {$tx['table']} WHERE nom = :nom AND id != :id", ['nom' => $nom, 'id' => $exclude_id]);
}

function taxonomie_insert(PDO $pdo, array $tx, string $nom): void
{
    query_run($pdo, "INSERT INTO {$tx['table']} (nom) VALUES (:nom)", ['nom' => $nom]);
}

function taxonomie_rename(PDO $pdo, array $tx, int $id, string $nom): void
{
    query_run($pdo, "UPDATE {$tx['table']} SET nom = :nom WHERE id = :id", ['nom' => $nom, 'id' => $id]);
}

function taxonomie_delete(PDO $pdo, array $tx, int $id): void
{
    query_run($pdo, "DELETE FROM {$tx['table']} WHERE id = :id", ['id' => $id]);
}
