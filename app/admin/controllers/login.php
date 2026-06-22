<?php
// app/admin/controllers/login.php

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
        } elseif ($operator['role'] !== 'admin') {
            $erreur = "Accès non autorisé.";
        } else {
            $_SESSION['operator_id']   = $operator['id'];
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
