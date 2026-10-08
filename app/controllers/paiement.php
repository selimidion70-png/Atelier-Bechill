<?php
// app/controllers/paiement.php
// Paiement d'une réservation qui vient d'être faite.
//  - Sans clé Mollie : paiement SIMULÉ (démonstration, aucune carte demandée).
//  - Avec une clé Mollie : le client paie sur la page sécurisée de Mollie puis revient ici (?retour=1).
require_once __DIR__ . '/../models/reservation.php';

// Après un choix, on affiche la confirmation une seule fois
if (isset($_SESSION['paiement_resultat'])) {
    $resultat = $_SESSION['paiement_resultat'];
    unset($_SESSION['paiement_resultat']);

    echo render($base . '/views/paiement.php', [
        'reservation' => reservation_get_by_id($pdo, $resultat['id']),
        'etape'       => $resultat['etape'],
        'erreur'      => '',
        'pageTitle'   => 'Réservation confirmée – ' . salon('nom'),
    ]);
    return;
}

// Seule la réservation qui vient d'être faite dans cette session peut être payée
$id          = (int)($_SESSION['reservation_a_payer'] ?? 0);
$reservation = $id > 0 ? reservation_get_by_id($pdo, $id) : false;

if (!$reservation) {
    redirect('/reservation');
}

// Termine le parcours : affiche la confirmation (après redirection, pour qu'un rechargement ne refasse rien)
function paiement_terminer(int $id, string $etape): never
{
    unset($_SESSION['reservation_a_payer']);
    $_SESSION['paiement_resultat'] = ['id' => $id, 'etape' => $etape];
    redirect('/paiement');
}

$erreur = '';

// Retour depuis la page de paiement Mollie : on demande le vrai statut à Mollie
if (isset($_GET['retour']) && mollie_actif() && $reservation['mollie_id']) {
    try {
        $statut = mollie_statut($reservation['mollie_id']);
        if ($statut === 'paid') {
            if (reservation_set_payee($pdo, $id)) {
                reservation_envoyer_email($pdo, $id, 'payee');
            }
            paiement_terminer($id, 'payee');
        }
        if (in_array($statut, ['open', 'pending', 'authorized'], true)) {
            paiement_terminer($id, 'en_cours');
        }
        $erreur = "Le paiement n'a pas abouti (annulé ou refusé). Vous pouvez réessayer ou payer sur place.";
    } catch (RuntimeException $e) {
        error_log($e->getMessage());
        $erreur = "Impossible de vérifier le paiement pour le moment. Réessayez dans un instant.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['choix'] ?? '') !== 'payer') {
        paiement_terminer($id, 'sur_place');
    }

    if (!mollie_actif()) {
        // Paiement simulé
        if (reservation_set_payee($pdo, $id)) {
            reservation_envoyer_email($pdo, $id, 'payee');
        }
        paiement_terminer($id, 'payee');
    }

    // Paiement Mollie : création du paiement puis envoi du client sur la page Mollie
    try {
        $paiement = mollie_creer_paiement(
            (float)$reservation['montant'],
            salon('nom') . ' – réservation n°' . $id . ' – ' . ($reservation['soin'] ?? 'soin'),
            config('url_site') . '/paiement?retour=1',
            mollie_url_webhook(),
            ['reservation_id' => $id]
        );
        reservation_set_mollie_id($pdo, $id, $paiement['id']);
        redirect($paiement['url']);
    } catch (RuntimeException $e) {
        error_log($e->getMessage());
        $erreur = "Le paiement en ligne est momentanément indisponible. Vous pouvez réessayer ou payer sur place.";
    }
}

echo render($base . '/views/paiement.php', [
    'reservation' => $reservation,
    'etape'       => 'choix',
    'erreur'      => $erreur,
    'pageTitle'   => 'Paiement – ' . salon('nom'),
]);
