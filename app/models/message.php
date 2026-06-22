<?php
// app/models/message.php
// Requêtes SQL liées aux messages de contact

function message_insert(PDO $pdo, array $data): void
{
    query_run($pdo, "
        INSERT INTO message (nom, email, sujet, texte, lu)
        VALUES (:nom, :email, :sujet, :texte, 0)
    ", $data);
}

function message_get_all(PDO $pdo, array $filtres = []): array
{
    $sql = "SELECT * FROM message WHERE 1=1";
    $params = [];

    if (!empty($filtres['recherche'])) {
        $sql .= " AND (nom LIKE :r1 OR sujet LIKE :r2)";
        $params['r1'] = '%' . $filtres['recherche'] . '%';
        $params['r2'] = '%' . $filtres['recherche'] . '%';
    }
    if (!empty($filtres['sujet'])) {
        $sql .= " AND sujet = :sujet";
        $params['sujet'] = $filtres['sujet'];
    }
    if (isset($filtres['lu']) && $filtres['lu'] !== '') {
        $sql .= " AND lu = :lu";
        $params['lu'] = $filtres['lu'] === 'lu' ? 1 : 0;
    }

    $sql .= " ORDER BY date_envoi DESC";

    return query_all($pdo, $sql, $params);
}

function message_set_lu(PDO $pdo, int $id, int $lu): void
{
    query_run($pdo, "UPDATE message SET lu = :lu WHERE id = :id", ['lu' => $lu, 'id' => $id]);
}

function message_delete(PDO $pdo, int $id): void
{
    query_run($pdo, "DELETE FROM message WHERE id = :id", ['id' => $id]);
}

function message_count_unread(PDO $pdo): int
{
    $result = query_one($pdo, "SELECT COUNT(*) AS total FROM message WHERE lu = 0");
    return (int)$result['total'];
}
