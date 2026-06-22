<?php
// core/query.php
// Fonctions utilitaires pour les requêtes PDO

// Exécute une requête SELECT et retourne tous les résultats
function query_all(PDO $pdo, string $sql, array $params = []): array
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Exécute une requête SELECT et retourne un seul résultat
function query_one(PDO $pdo, string $sql, array $params = []): array|false
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

// Exécute une requête INSERT/UPDATE/DELETE
function query_run(PDO $pdo, string $sql, array $params = []): bool
{
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

// Retourne le dernier ID inséré
function query_last_id(PDO $pdo): string
{
    return $pdo->lastInsertId();
}
