<?php
// app/controllers/paiement-webhook.php
// Appelé par Stripe (pas par un visiteur) quand un paiement est terminé.
// On vérifie la signature de Stripe, puis on redemande toujours le statut à Stripe
// (on ne fait jamais confiance au contenu de la requête). Exempté du jeton CSRF dans index.php.
// À déclarer dans le tableau de bord Stripe → Développeurs → Webhooks (voir README).
require_once __DIR__ . '/../models/reservation.php';

$secret    = (string)config('stripe_webhook_secret');
$corps     = (string)file_get_contents('php://input');
$signature = (string)($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');

if (!stripe_actif() || $secret === '' || !stripe_signature_valide($corps, $signature, $secret)) {
    http_out(400, '');
    exit;
}

$evenement   = json_decode($corps, true) ?? [];
$sessionId   = (string)($evenement['data']['object']['id'] ?? '');
$utile       = in_array($evenement['type'] ?? '', ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true);
$reservation = $utile && $sessionId !== '' ? reservation_get_by_paiement_id($pdo, $sessionId) : false;

if ($reservation) {
    try {
        if (stripe_statut($sessionId) === 'paid' && reservation_set_payee($pdo, (int)$reservation['id'])) {
            reservation_envoyer_email($pdo, (int)$reservation['id'], 'payee');
        }
    } catch (RuntimeException $e) {
        error_log($e->getMessage());
        http_response_code(500); // Stripe réessaiera plus tard
    }
}

// Réponse vide : Stripe n'attend que le code HTTP
http_out(http_response_code() ?: 200, '');
exit;
