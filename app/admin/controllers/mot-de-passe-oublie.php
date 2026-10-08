<?php
// Demande d'un lien de réinitialisation du mot de passe (accessible sans être connecté)
require_once __DIR__ . '/../../models/operator.php';

$envoye = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $compte = operator_creer_jeton_reset($pdo, trim($_POST['email'] ?? ''));

    if ($compte) {
        $lien = config('url_site') . '/admin/reinitialiser?jeton=' . $compte['jeton'];
        mail_envoyer($compte['email'], salon('nom') . ' – Réinitialisation de votre mot de passe',
            "Bonjour {$compte['nom']},\n\n"
            . "Une demande de nouveau mot de passe a été faite pour votre compte d'administration.\n"
            . "Pour choisir un nouveau mot de passe, ouvrez ce lien (valable 1 heure, une seule fois) :\n\n"
            . "$lien\n\n"
            . "Si vous n'êtes pas à l'origine de cette demande, ignorez cet email : votre mot de passe actuel reste valable.\n\n"
            . mail_signature());
    }

    // Même message dans tous les cas : on ne révèle pas si l'adresse correspond à un compte
    $envoye = true;
}

echo render($base . '/views/mot-de-passe-oublie.php', [
    'envoye'    => $envoye,
    'pageTitle' => 'Mot de passe oublié – Administration',
]);
