<?php
// Gestion des comptes admin / éditeur (page réservée à l'administrateur, voir app/admin/acces.php)
require_once __DIR__ . '/../../models/operator.php';

$erreurs = [];
$moi     = (int)$_SESSION['operator_id'];
$nouveau = ['nom' => '', 'email' => '', 'role' => 'editeur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    // On ne peut pas se retirer ses propres droits (sinon plus personne ne pourrait gérer les comptes)
    if (in_array($action, ['role', 'actif'], true) && $id === $moi) {
        $erreurs[] = "Vous ne pouvez pas modifier votre propre rôle ni désactiver votre propre compte.";
    } elseif ($action === 'ajouter') {
        $nouveau = [
            'nom'   => trim($_POST['nom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'role'  => $_POST['role'] ?? 'editeur',
        ];
        $mdp = $_POST['mot_de_passe'] ?? '';

        if ($nouveau['nom'] === '') $erreurs[] = "Le nom est obligatoire.";
        if (!filter_var($nouveau['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = "L'adresse courriel est invalide.";
        } elseif (operator_email_existe($pdo, $nouveau['email'])) {
            $erreurs[] = "Un compte existe déjà avec cette adresse.";
        }
        if (mb_strlen($mdp) < OPERATOR_MDP_MIN) $erreurs[] = "Le mot de passe doit faire au moins " . OPERATOR_MDP_MIN . " caractères.";
        if (!isset(ADMIN_ROLES[$nouveau['role']])) $erreurs[] = "Rôle invalide.";

        if (empty($erreurs)) {
            operator_insert($pdo, $nouveau['nom'], $nouveau['email'], $mdp, $nouveau['role']);
        }
    } elseif ($action === 'role' && isset(ADMIN_ROLES[$_POST['role'] ?? ''])) {
        operator_set_role($pdo, $id, $_POST['role']);
    } elseif ($action === 'actif') {
        operator_set_actif($pdo, $id, ($_POST['actif'] ?? '') === '1');
    } elseif ($action === 'mot_de_passe') {
        $mdp = $_POST['mot_de_passe'] ?? '';
        if (mb_strlen($mdp) < OPERATOR_MDP_MIN) {
            $erreurs[] = "Le mot de passe doit faire au moins " . OPERATOR_MDP_MIN . " caractères.";
        } else {
            operator_set_mot_de_passe($pdo, $id, $mdp);
        }
    }

    if (empty($erreurs)) {
        redirect('/admin/utilisateurs');
    }
}

echo render($base . '/views/utilisateurs.php', [
    'utilisateurs' => operator_get_all($pdo),
    'moi'          => $moi,
    'erreurs'      => $erreurs,
    'nouveau'      => $nouveau,
    'pageTitle'    => 'Utilisateurs – Admin',
]);
