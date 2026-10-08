<?php
// app/admin/controllers/login.php

// Déjà connecté : pas besoin de revoir le formulaire
if (!empty($_SESSION['operator_id'])) {
    redirect('/admin');
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['identifiant'] ?? '');
    $motDePasse = $_POST['mot-de-passe'] ?? '';

    if ($email === '' || $motDePasse === '') {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $operator = query_one($pdo, "SELECT * FROM operator WHERE email = :email", ['email' => $email]);

        if (!$operator || !$operator['actif']) {
            $erreur = "Identifiant ou mot de passe incorrect.";
        } elseif (!password_verify($motDePasse, $operator['mot_de_passe'])) {
            $erreur = "Identifiant ou mot de passe incorrect.";
        } elseif (!isset(ADMIN_ROLES[$operator['role']])) {
            $erreur = "Accès non autorisé.";
        } else {
            // Nouvel identifiant de session à la connexion (protection contre le vol de session)
            session_regenerate_id(true);
            $_SESSION['operator_id']  = $operator['id'];
            $_SESSION['operator_nom']  = $operator['nom'];
            $_SESSION['operator_role'] = $operator['role'];
            redirect('/admin');
        }
    }
}

echo render($base . '/views/login.php', [
    'erreur'    => $erreur,
    'pageTitle' => 'Connexion – Administration BE CHILL',
]);
