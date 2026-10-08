<?php
// core/mollie.php
// Appels à l'API de paiement Mollie (https://docs.mollie.com), sans bibliothèque externe.
// Le paiement en ligne est actif seulement si une clé est définie dans config/app.local.php.

function mollie_actif(): bool
{
    return (string)config('mollie_cle') !== '';
}

// Envoie une requête à Mollie et retourne la réponse décodée.
// Lève une RuntimeException si Mollie est injoignable ou refuse la requête.
function mollie_requete(string $methode, string $chemin, ?array $donnees = null): array
{
    $url = rtrim(config('mollie_api_url') ?: 'https://api.mollie.com/v2', '/') . $chemin;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $methode,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . config('mollie_cle'),
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_POSTFIELDS     => $donnees !== null ? json_encode($donnees) : null,
    ]);
    $corps = curl_exec($ch);
    $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err   = curl_error($ch);
    curl_close($ch);

    if ($corps === false) {
        throw new RuntimeException("Mollie injoignable : $err");
    }
    $reponse = json_decode($corps, true) ?? [];
    if ($code >= 400) {
        throw new RuntimeException("Mollie a refusé la requête ($code) : " . ($reponse['detail'] ?? $corps));
    }
    return $reponse;
}

// Crée un paiement et retourne ['id' => 'tr_…', 'url' => page de paiement Mollie]
function mollie_creer_paiement(float $montant, string $description, string $urlRetour, ?string $urlWebhook, array $metadata): array
{
    $donnees = [
        'amount'      => ['currency' => 'EUR', 'value' => number_format($montant, 2, '.', '')],
        'description' => $description,
        'redirectUrl' => $urlRetour,
        'metadata'    => $metadata,
    ];
    // Mollie doit pouvoir joindre le webhook : impossible depuis un ordinateur local
    if ($urlWebhook !== null) {
        $donnees['webhookUrl'] = $urlWebhook;
    }

    $paiement = mollie_requete('POST', '/payments', $donnees);
    return ['id' => $paiement['id'], 'url' => $paiement['_links']['checkout']['href']];
}

// Statut d'un paiement : open, pending, authorized, paid, canceled, expired, failed
function mollie_statut(string $id): string
{
    if (!preg_match('/^tr_[A-Za-z0-9]+$/', $id)) {
        throw new RuntimeException("Identifiant de paiement Mollie invalide.");
    }
    return mollie_requete('GET', '/payments/' . $id)['status'] ?? 'inconnu';
}

// Le webhook n'est utilisable que si le site est public (https, pas d'adresse locale)
function mollie_url_webhook(): ?string
{
    $site = (string)config('url_site');
    $hote = (string)parse_url($site, PHP_URL_HOST);
    $local = $hote === 'localhost' || str_ends_with($hote, '.test') || str_ends_with($hote, '.local');
    return str_starts_with($site, 'https://') && !$local ? $site . '/paiement-webhook' : null;
}
