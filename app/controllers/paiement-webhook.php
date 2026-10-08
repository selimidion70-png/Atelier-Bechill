<?php
// app/controllers/paiement-webhook.php
// Appelé par Mollie (pas par un visiteur) quand un paiement change d'état.
// Mollie envoie seulement l'identifiant : on redemande toujours le statut à Mollie
// (on ne fait jamais confiance au contenu de la requête). Exempté du jeton CSRF dans index.php.
require_once __DIR__ . '/../models/reservation.php';

$mollieId    = (string)($_POST['id'] ?? '');
$reservation = mollie_actif() && $mollieId !== '' ? reservation_get_by_mollie_id($pdo, $mollieId) : false;

if ($reservation) {
    try {
        if (mollie_statut($mollieId) === 'paid' && reservation_set_payee($pdo, (int)$reservation['id'])) {
            reservation_envoyer_email($pdo, (int)$reservation['id'], 'payee');
        }
    } catch (RuntimeException $e) {
        error_log($e->getMessage());
        http_response_code(500); // Mollie réessaiera plus tard
    }
}

// Réponse vide : Mollie n'attend que le code HTTP
http_out(http_response_code() ?: 200, '');
exit;
