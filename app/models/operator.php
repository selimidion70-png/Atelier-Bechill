<?php
// app/models/operator.php
// Comptes de l'espace d'administration (admin / éditeur)

const OPERATOR_MDP_MIN = 8;

function operator_get_all(PDO $pdo): array
{
    return query_all($pdo, "
        SELECT id, nom, email, role, actif, date_creation
        FROM operator
        WHERE role IN ('admin', 'editeur')
        ORDER BY nom
    ");
}

function operator_email_existe(PDO $pdo, string $email): bool
{
    return (bool)query_one($pdo, "SELECT id FROM operator WHERE email = :email", ['email' => $email]);
}

function operator_insert(PDO $pdo, string $nom, string $email, string $motDePasse, string $role): void
{
    query_run($pdo, "
        INSERT INTO operator (nom, email, mot_de_passe, role, actif)
        VALUES (:nom, :email, :mdp, :role, 1)
    ", [
        'nom'   => $nom,
        'email' => $email,
        'mdp'   => password_hash($motDePasse, PASSWORD_DEFAULT),
        'role'  => $role,
    ]);
}

function operator_set_role(PDO $pdo, int $id, string $role): void
{
    query_run($pdo, "UPDATE operator SET role = :role WHERE id = :id", ['role' => $role, 'id' => $id]);
}

function operator_set_actif(PDO $pdo, int $id, bool $actif): void
{
    query_run($pdo, "UPDATE operator SET actif = :actif WHERE id = :id", ['actif' => (int)$actif, 'id' => $id]);
}

function operator_set_mot_de_passe(PDO $pdo, int $id, string $motDePasse): void
{
    query_run($pdo, "UPDATE operator SET mot_de_passe = :mdp WHERE id = :id", [
        'mdp' => password_hash($motDePasse, PASSWORD_DEFAULT),
        'id'  => $id,
    ]);
}
