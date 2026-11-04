<?php
// core/stripe.php
// Appels à l'API de paiement Stripe (https://docs.stripe.com), sans bibliothèque externe.
// Le paiement en ligne est actif seulement si une clé est définie dans config/app.local.php.
// Les moyens de paiement proposés (Bancontact, cartes, Apple Pay…) se choisissent
// dans le tableau de bord Stripe → Paramètres → Moyens de paiement.

function stripe_actif(): bool
{
    return (string)config('stripe_cle') !== '';
}

// Envoie une requête à Stripe et retourne la réponse décodée.
// Lève une RuntimeException si Stripe est injoignable ou refuse la requête.
function stripe_requete(string $methode, string $chemin, ?array $donnees = null): array
{
    $url = 'https://api.stripe.com/v1' . $chemin;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $methode,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . config('stripe_cle'),
            'Accept: application/json',
        ],
        // Stripe attend les données en formulaire : line_items[0][price_data][currency]=eur…
        CURLOPT_POSTFIELDS     => $donnees !== null ? http_build_query($donnees) : null,
    ]);
    $corps = curl_exec($ch);
    $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err   = curl_error($ch);
    curl_close($ch);

    if ($corps === false) {
        throw new RuntimeException("Stripe injoignable : $err");
    }
    $reponse = json_decode($corps, true) ?? [];
    if ($code >= 400) {
        throw new RuntimeException("Stripe a refusé la requête ($code) : " . ($reponse['error']['message'] ?? $corps));
    }
    return $reponse;
}

// Crée une page de paiement Stripe Checkout et retourne ['id' => 'cs_…', 'url' => page de paiement Stripe]
function stripe_creer_paiement(float $montant, string $description, string $urlRetour, string $urlAnnulation, array $metadata): array
{
    $session = stripe_requete('POST', '/checkout/sessions', [
        'mode'        => 'payment',
        'locale'      => 'fr',
        'line_items'  => [[
            'quantity'   => 1,
            'price_data' => [
                'currency'     => 'eur',
                'unit_amount'  => (int)round($montant * 100), // en centimes
                'product_data' => ['name' => $description],
            ],
        ]],
        'success_url' => $urlRetour,
        'cancel_url'  => $urlAnnulation,
        'metadata'    => $metadata,
    ]);
    return ['id' => $session['id'], 'url' => $session['url']];
}

// Statut d'un paiement : paid (payé), pending (payé mais pas encore confirmé par la banque),
// open (pas encore payé), expired (abandonné)
function stripe_statut(string $id): string
{
    if (!preg_match('/^cs_[A-Za-z0-9_]+$/', $id)) {
        throw new RuntimeException("Identifiant de paiement Stripe invalide.");
    }
    $session = stripe_requete('GET', '/checkout/sessions/' . $id);

    if (in_array($session['payment_status'] ?? '', ['paid', 'no_payment_required'], true)) {
        return 'paid';
    }
    return match ($session['status'] ?? '') {
        'complete' => 'pending',
        'expired'  => 'expired',
        default    => 'open',
    };
}

// Vérifie que la notification (webhook) vient bien de Stripe grâce à sa signature.
// L'en-tête ressemble à : t=1700000000,v1=5257a869e7…
function stripe_signature_valide(string $corps, string $entete, string $secret): bool
{
    $parties = [];
    foreach (explode(',', $entete) as $element) {
        [$cle, $valeur] = array_pad(explode('=', trim($element), 2), 2, '');
        $parties[$cle][] = $valeur;
    }
    $horodatage = (int)($parties['t'][0] ?? 0);

    // Refuse les notifications de plus de 5 minutes (rejeu d'une ancienne requête)
    if ($horodatage === 0 || abs(time() - $horodatage) > 300) {
        return false;
    }
    $attendu = hash_hmac('sha256', $horodatage . '.' . $corps, $secret);
    foreach ($parties['v1'] ?? [] as $signature) {
        if (hash_equals($attendu, $signature)) {
            return true;
        }
    }
    return false;
}
