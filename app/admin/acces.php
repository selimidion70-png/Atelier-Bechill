<?php
// app/admin/acces.php
// Qui peut ouvrir quelle page de l'admin.
//  - admin   : tout
//  - editeur : le contenu du site (soins, catégories, thèmes, tags, collections)

const ADMIN_ROLES = ['admin' => 'Administrateur', 'editeur' => 'Éditeur'];

// Pages de l'admin accessibles sans être connecté
const ADMIN_PAGES_PUBLIQUES = ['login', 'mot-de-passe-oublie', 'reinitialiser'];

// Pages réservées à l'administrateur (données des clients et gestion des comptes)
const ADMIN_PAGES_ADMIN_SEULEMENT = ['reservations', 'messages', 'utilisateurs', 'donnees-client'];

function admin_peut(string $role, string $route): bool
{
    if ($role === 'admin') {
        return true;
    }
    return $role === 'editeur' && !in_array($route, ADMIN_PAGES_ADMIN_SEULEMENT, true);
}

function admin_est_admin(): bool
{
    return ($_SESSION['operator_role'] ?? '') === 'admin';
}
