<?php

$erreurs = [];
$succes  = false;
$valeurs = [
    'nom'                => '',
    'prenom'             => '',
    'email'              => '',
    'telephone'          => '',
    'soin'               => '',
    'date'               => '',
    'heure'              => '',
    'preference-contact' => 'email',
    'remarques'          => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs = [
        'nom'                => trim($_POST['nom'] ?? ''),
        'prenom'             => trim($_POST['prenom'] ?? ''),
        'email'              => trim($_POST['email'] ?? ''),
        'telephone'          => trim($_POST['telephone'] ?? ''),
        'soin'               => $_POST['soin'] ?? '',
        'date'               => $_POST['date'] ?? '',
        'heure'              => $_POST['heure'] ?? '',
        'preference-contact' => $_POST['preference-contact'] ?? 'email',
        'remarques'          => trim($_POST['remarques'] ?? ''),
    ];

    if ($valeurs['nom'] === '')    $erreurs[] = "Le nom est obligatoire.";
    if ($valeurs['prenom'] === '') $erreurs[] = "Le prénom est obligatoire.";
    if (!filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'adresse courriel est invalide.";
    if ($valeurs['soin'] === '')   $erreurs[] = "Veuillez choisir un soin.";
    if ($valeurs['date'] === '')   $erreurs[] = "Veuillez choisir une date.";
    if ($valeurs['heure'] === '')  $erreurs[] = "Veuillez choisir une heure.";
    if (!isset($_POST['conditions'])) $erreurs[] = "Vous devez accepter les conditions d'annulation.";

    if (empty($erreurs)) {
        $succes  = true;
        $valeurs = [
            'nom' => '', 'prenom' => '', 'email' => '', 'telephone' => '',
            'soin' => '', 'date' => '', 'heure' => '',
            'preference-contact' => 'email', 'remarques' => '',
        ];
    }
}

echo render($base . '/views/reservation.php', [
    'erreurs'   => $erreurs,
    'succes'    => $succes,
    'valeurs'   => $valeurs,
    'pageTitle' => 'Réservation – BE CHILL',
]);
