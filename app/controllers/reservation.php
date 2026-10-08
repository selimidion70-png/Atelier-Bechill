<?php
// app/controllers/reservation.php
require_once __DIR__ . '/../models/item.php';
require_once __DIR__ . '/../models/reservation.php';

$erreurs = [];
$succes  = false;
$vide    = [
    'nom'                => '',
    'prenom'             => '',
    'email'              => '',
    'telephone'          => '',
    'soin'               => $_GET['soin'] ?? '',
    'date'               => '',
    'heure'              => '',
    'preference-contact' => 'email',
    'remarques'          => '',
];
$valeurs = $vide;

// Soins publiés, regroupés par catégorie pour la liste déroulante
$soinsPublies      = item_get_all_published($pdo);
$idsSoins          = array_map('intval', array_column($soinsPublies, 'id'));
$soinsParCategorie = [];
foreach ($soinsPublies as $soin) {
    $soinsParCategorie[$soin['categorie']][] = $soin;
}

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

    $date = DateTime::createFromFormat('!Y-m-d', $valeurs['date']);

    if ($valeurs['nom'] === '')    $erreurs[] = "Le nom est obligatoire.";
    if ($valeurs['prenom'] === '') $erreurs[] = "Le prénom est obligatoire.";
    if (!filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'adresse courriel est invalide.";
    if (!in_array((int)$valeurs['soin'], $idsSoins, true)) $erreurs[] = "Veuillez choisir un soin.";
    if (!$date || $date->format('Y-m-d') !== $valeurs['date']) {
        $erreurs[] = "Veuillez choisir une date.";
    } elseif ($date < new DateTime('today')) {
        $erreurs[] = "La date ne peut pas être dans le passé.";
    }
    if (!in_array($valeurs['heure'], RESERVATION_HEURES, true)) $erreurs[] = "Veuillez choisir une heure.";
    if (!in_array($valeurs['preference-contact'], ['email', 'telephone'], true)) $valeurs['preference-contact'] = 'email';
    if ($valeurs['preference-contact'] === 'telephone' && $valeurs['telephone'] === '') {
        $erreurs[] = "Indiquez un numéro de téléphone pour être contacté par téléphone.";
    }
    if (!isset($_POST['conditions'])) $erreurs[] = "Vous devez accepter les conditions d'annulation.";

    if (empty($erreurs)) {
        reservation_insert($pdo, [
            'nom'                => $valeurs['nom'],
            'prenom'             => $valeurs['prenom'],
            'email'              => $valeurs['email'],
            'telephone'          => $valeurs['telephone'] !== '' ? $valeurs['telephone'] : null,
            'item_id'            => (int)$valeurs['soin'],
            'date_rdv'           => $valeurs['date'],
            'heure_rdv'          => $valeurs['heure'],
            'preference_contact' => $valeurs['preference-contact'],
            'remarques'          => $valeurs['remarques'] !== '' ? $valeurs['remarques'] : null,
        ]);

        $succes  = true;
        $valeurs = $vide;
    }
}

echo render($base . '/views/reservation.php', [
    'erreurs'           => $erreurs,
    'succes'            => $succes,
    'valeurs'           => $valeurs,
    'soinsParCategorie' => $soinsParCategorie,
    'heures'            => RESERVATION_HEURES,
    'pageTitle'         => 'Réservation – BE CHILL',
]);
