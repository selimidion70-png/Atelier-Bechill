<?php
// app/controllers/paiement.php
// Paiement SIMULÉ d'une réservation (démonstration : aucune carte n'est demandée ni débitée)
require_once __DIR__ . '/../models/reservation.php';

// Après un choix (payer / sur place), on affiche la confirmation une seule fois
if (isset($_SESSION['paiement_resultat'])) {
    $resultat = $_SESSION['paiement_resultat'];
    unset($_SESSION['paiement_resultat']);

    echo render($base . '/views/paiement.php', [
        'reservation' => reservation_get_by_id($pdo, $resultat['id']),
        'etape'       => $resultat['etape'],
        'pageTitle'   => 'Réservation confirmée – BE CHILL',
    ]);
    return;
}

// Seule la réservation qui vient d'être faite dans cette session peut être payée
$id          = (int)($_SESSION['reservation_a_payer'] ?? 0);
$reservation = $id > 0 ? reservation_get_by_id($pdo, $id) : false;

if (!$reservation) {
    redirect('/reservation');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $etape = ($_POST['choix'] ?? '') === 'payer' ? 'payee' : 'sur_place';

    if ($etape === 'payee') {
        reservation_set_payee($pdo, $id);
    }

    unset($_SESSION['reservation_a_payer']);
    $_SESSION['paiement_resultat'] = ['id' => $id, 'etape' => $etape];
    redirect('/paiement');
}

echo render($base . '/views/paiement.php', [
    'reservation' => $reservation,
    'etape'       => 'choix',
    'pageTitle'   => 'Paiement – BE CHILL',
]);
