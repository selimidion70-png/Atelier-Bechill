<?php
// Choix d'un nouveau mot de passe depuis le lien reçu par email (accessible sans être connecté)
require_once __DIR__ . '/../../models/operator.php';

// Le jeton est dans l'adresse : on évite qu'il soit transmis à d'autres sites via le « Referer »
header('Referrer-Policy: no-referrer');

$jeton   = (string)($_GET['jeton'] ?? '');
$compte  = operator_par_jeton_reset($pdo, $jeton);
$erreurs = [];
$termine = false;

if ($compte && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $mdp          = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    if (mb_strlen($mdp) < OPERATOR_MDP_MIN) {
        $erreurs[] = "Le mot de passe doit faire au moins " . OPERATOR_MDP_MIN . " caractères.";
    } elseif ($mdp !== $confirmation) {
        $erreurs[] = "Les deux mots de passe ne sont pas identiques.";
    } else {
        // Le jeton est effacé en même temps : le lien ne fonctionne qu'une fois
        operator_set_mot_de_passe($pdo, (int)$compte['id'], $mdp);
        $termine = true;
    }
}

echo render($base . '/views/reinitialiser.php', [
    'jeton'     => $jeton,
    'valide'    => (bool)$compte,
    'termine'   => $termine,
    'erreurs'   => $erreurs,
    'pageTitle' => 'Nouveau mot de passe – Administration',
]);
