<?php
// app/controllers/contact.php
require_once __DIR__ . '/../models/message.php';

$erreurs = [];
$succes  = false;
$valeurs = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '', 'sujet' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs = [
        'nom'       => trim($_POST['nom'] ?? ''),
        'prenom'    => trim($_POST['prenom'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'sujet'     => $_POST['sujet'] ?? '',
        'message'   => trim($_POST['message'] ?? ''),
    ];

    if ($valeurs['nom'] === '')    $erreurs[] = "Le nom est obligatoire.";
    if ($valeurs['prenom'] === '') $erreurs[] = "Le prénom est obligatoire.";
    if (!filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) $erreurs[] = "Courriel invalide.";
    if ($valeurs['sujet'] === '')  $erreurs[] = "Veuillez choisir un sujet.";
    if ($valeurs['message'] === '') $erreurs[] = "Le message est obligatoire.";

    if (empty($erreurs)) {
        $texte = $valeurs['message'];
        if ($valeurs['telephone'] !== '') {
            $texte .= "\n\nTéléphone : " . $valeurs['telephone'];
        }

        message_insert($pdo, [
            'nom'   => $valeurs['prenom'] . ' ' . $valeurs['nom'],
            'email' => $valeurs['email'],
            'sujet' => $valeurs['sujet'],
            'texte' => $texte,
        ]);

        $succes  = true;
        $valeurs = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '', 'sujet' => '', 'message' => ''];
    }
}

echo render($base . '/views/contact.php', [
    'erreurs'   => $erreurs,
    'succes'    => $succes,
    'valeurs'   => $valeurs,
    'pageTitle' => 'Contact – BE CHILL',
]);
