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

// Change le mot de passe et annule un éventuel lien « mot de passe oublié » en cours
function operator_set_mot_de_passe(PDO $pdo, int $id, string $motDePasse): void
{
    query_run($pdo, "UPDATE operator SET mot_de_passe = :mdp, reset_jeton = NULL, reset_expire = NULL WHERE id = :id", [
        'mdp' => password_hash($motDePasse, PASSWORD_DEFAULT),
        'id'  => $id,
    ]);
}

// ---- Mot de passe oublié ----

// Crée un lien de réinitialisation pour ce compte (actif, admin ou éditeur).
// Retourne le jeton à mettre dans le lien, ou null si l'email ne correspond à aucun compte valable.
// Seul le hash du jeton est stocké : une fuite de la base ne permet pas d'utiliser le lien.
function operator_creer_jeton_reset(PDO $pdo, string $email): ?array
{
    $op = query_one($pdo, "
        SELECT id, nom, email FROM operator
        WHERE email = :email AND actif = 1 AND role IN ('admin', 'editeur')
    ", ['email' => $email]);
    if (!$op) {
        return null;
    }

    $jeton = bin2hex(random_bytes(32));
    query_run($pdo, "
        UPDATE operator SET reset_jeton = :hash, reset_expire = DATE_ADD(NOW(), INTERVAL 1 HOUR)
        WHERE id = :id
    ", ['hash' => hash('sha256', $jeton), 'id' => $op['id']]);

    return $op + ['jeton' => $jeton];
}

// Compte correspondant à un jeton encore valable (ou false)
function operator_par_jeton_reset(PDO $pdo, string $jeton): array|false
{
    if (!preg_match('/^[0-9a-f]{64}$/', $jeton)) {
        return false;
    }
    return query_one($pdo, "
        SELECT id, nom FROM operator
        WHERE reset_jeton = :hash AND reset_expire > NOW() AND actif = 1
    ", ['hash' => hash('sha256', $jeton)]);
}
