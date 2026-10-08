<?php
// app/controllers/reservation.php
require_once __DIR__ . '/../models/item.php';
require_once __DIR__ . '/../models/reservation.php';

$erreurs = [];
$valeurs = [
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

// Soins publiés, regroupés par catégorie pour la liste déroulante
$soinsPublies      = item_get_all_published($pdo);
$dureeParSoin      = array_map('intval', array_column($soinsPublies, 'duree', 'id'));
$soinsParCategorie = [];
foreach ($soinsPublies as $soin) {
    $soinsParCategorie[$soin['categorie']][] = $soin;
}

// Appel JavaScript : heures libres pour une date et un soin → réponse JSON
if (isset($_GET['creneaux'])) {
    $date  = (string)($_GET['date'] ?? '');
    $duree = $dureeParSoin[(int)($_GET['soin'] ?? 0)] ?? 60;
    $ok    = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && $date >= date('Y-m-d');

    header('Content-Type: application/json');
    echo json_encode([
        'ferme'       => $ok && RESERVATION_OUVERTURE[(int)date('N', strtotime($date))] === null,
        'disponibles' => $ok ? reservation_heures_disponibles($pdo, $date, $duree) : [],
    ]);
    exit;
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
    $soinValide = isset($dureeParSoin[(int)$valeurs['soin']]);
    $dateValide = $date && $date->format('Y-m-d') === $valeurs['date'];
    if (!$soinValide) $erreurs[] = "Veuillez choisir un soin.";
    if (!$dateValide) {
        $erreurs[] = "Veuillez choisir une date.";
    } elseif ($date < new DateTime('today')) {
        $erreurs[] = "La date ne peut pas être dans le passé.";
        $dateValide = false;
    } elseif (RESERVATION_OUVERTURE[(int)$date->format('N')] === null) {
        $erreurs[] = "Le salon est fermé le dimanche.";
        $dateValide = false;
    }
    if (!in_array($valeurs['heure'], RESERVATION_HEURES, true)) {
        $erreurs[] = "Veuillez choisir une heure.";
    } elseif ($soinValide && $dateValide
        && !in_array($valeurs['heure'], reservation_heures_disponibles($pdo, $valeurs['date'], $dureeParSoin[(int)$valeurs['soin']]), true)) {
        $erreurs[] = "Ce créneau n'est pas disponible (déjà réservé ou trop proche de la fermeture). Choisissez une autre heure.";
    }
    if (!in_array($valeurs['preference-contact'], ['email', 'telephone'], true)) $valeurs['preference-contact'] = 'email';
    if ($valeurs['preference-contact'] === 'telephone' && $valeurs['telephone'] === '') {
        $erreurs[] = "Indiquez un numéro de téléphone pour être contacté par téléphone.";
    }
    if (!isset($_POST['conditions'])) $erreurs[] = "Vous devez accepter les conditions d'annulation.";

    if (empty($erreurs)) {
        $id = reservation_insert($pdo, [
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

        reservation_envoyer_email($pdo, $id, 'recue');

        // Seule la personne qui vient de réserver peut payer cette réservation
        $_SESSION['reservation_a_payer'] = $id;
        redirect('/paiement');
    }
}

echo render($base . '/views/reservation.php', [
    'erreurs'           => $erreurs,
    'valeurs'           => $valeurs,
    'soinsParCategorie' => $soinsParCategorie,
    'heures'            => RESERVATION_HEURES,
    'pageTitle'         => 'Réservation – BE CHILL',
]);
