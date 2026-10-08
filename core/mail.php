<?php
// core/mail.php
// Envoi d'emails texte en UTF-8.
// En local avec Laragon, les emails sont capturés par Mailpit (http://localhost:8025) : rien ne part vraiment.

// Retourne true si l'email a été remis au serveur de mail.
// Un échec n'interrompt jamais la page : il est seulement noté dans le journal d'erreurs.
function mail_envoyer(string $destinataire, string $sujet, string $texte): bool
{
    if (!filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $entetes = implode("\r\n", [
        'From: ' . config('email_expediteur'),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ]);

    $ok = @mail($destinataire, mb_encode_mimeheader($sujet, 'UTF-8'), $texte, $entetes);
    if (!$ok) {
        error_log("BE CHILL : échec de l'envoi de l'email « $sujet » à $destinataire");
    }
    return $ok;
}

// Signature ajoutée à la fin des emails (coordonnées du salon, depuis config/app.php)
function mail_signature(): string
{
    return "L'équipe " . salon('nom') . "\n"
         . salon('adresse') . ", " . salon('code_postal') . " " . salon('ville') . " – " . salon('telephone');
}
