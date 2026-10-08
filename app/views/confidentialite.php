<?php
// Modèle de politique de confidentialité (RGPD). Il décrit ce que fait réellement le code du site :
// à adapter si le site change, et à faire relire par le client avant la mise en ligne.
$moisReservations = (int)config('rgpd_conservation_reservations');
$moisMessages     = (int)config('rgpd_conservation_messages');
$paiementEnLigne  = config('mollie_cle') !== '';
?>
<main>
    <h1>Politique de confidentialité</h1>
    <p>Cette page explique quelles données personnelles nous recueillons sur ce site, pourquoi, combien de temps nous les gardons et quels sont vos droits, conformément au Règlement général sur la protection des données (RGPD).</p>

    <section aria-labelledby="responsable">
        <h2 id="responsable">Responsable du traitement</h2>
        <p>
            <?= htmlspecialchars(salon('nom')) ?>, <?= htmlspecialchars(salon('adresse')) ?>, <?= htmlspecialchars(salon('code_postal') . ' ' . salon('ville')) ?>.
            Pour toute question sur vos données : <a href="mailto:<?= htmlspecialchars(salon('email')) ?>"><?= htmlspecialchars(salon('email')) ?></a>.
        </p>
    </section>

    <section aria-labelledby="donnees-collectees">
        <h2 id="donnees-collectees">Données recueillies et utilisation</h2>
        <table>
            <caption>Données personnelles traitées par le site</caption>
            <thead>
                <tr>
                    <th scope="col">Quand</th>
                    <th scope="col">Données</th>
                    <th scope="col">Pourquoi</th>
                    <th scope="col">Durée de conservation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Réservation</td>
                    <td>Nom, prénom, email, téléphone (facultatif), soin, date et heure, remarques, statut du paiement</td>
                    <td>Organiser votre rendez-vous et vous contacter à son sujet (exécution de votre demande)</td>
                    <td><?= $moisReservations ?> mois après la date du rendez-vous</td>
                </tr>
                <tr>
                    <td>Formulaire de contact</td>
                    <td>Nom, prénom, email, téléphone (facultatif), sujet, message</td>
                    <td>Répondre à votre message (intérêt légitime)</td>
                    <td><?= $moisMessages ?> mois après l'envoi</td>
                </tr>
            </tbody>
        </table>
        <p>Les données sont supprimées automatiquement à la fin de ces durées. Elles ne sont jamais vendues ni utilisées à des fins publicitaires.</p>
    </section>

    <section aria-labelledby="destinataires">
        <h2 id="destinataires">Qui a accès à vos données ?</h2>
        <ul>
            <li>Uniquement l'équipe de <?= htmlspecialchars(salon('nom')) ?> qui gère les réservations et les messages.</li>
            <?php if ($paiementEnLigne): ?>
                <li>En cas de paiement en ligne : notre prestataire de paiement <strong>Mollie B.V.</strong> (Pays-Bas). Vos données de carte sont saisies chez Mollie et ne passent jamais par notre site.</li>
            <?php else: ?>
                <li>Le paiement en ligne de ce site est une démonstration : aucune donnée de carte n'est demandée.</li>
            <?php endif; ?>
        </ul>
    </section>

    <section aria-labelledby="cookies">
        <h2 id="cookies">Cookies</h2>
        <p>Ce site utilise un seul cookie, strictement nécessaire à son fonctionnement (cookie de session, supprimé à la fermeture du navigateur). Il sert à sécuriser les formulaires et à garder votre réservation pendant le paiement. Aucun cookie publicitaire ni de mesure d'audience n'est utilisé : aucun consentement n'est donc demandé.</p>
    </section>

    <section aria-labelledby="droits">
        <h2 id="droits">Vos droits</h2>
        <p>Vous pouvez à tout moment demander à consulter, corriger ou supprimer vos données, ou vous opposer à leur utilisation. Écrivez-nous à <a href="mailto:<?= htmlspecialchars(salon('email')) ?>"><?= htmlspecialchars(salon('email')) ?></a> : nous vous répondons dans un délai d'un mois.</p>
        <p>Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire une plainte auprès de l'Autorité de protection des données : <a href="https://www.autoriteprotectiondonnees.be">www.autoriteprotectiondonnees.be</a>.</p>
    </section>

    <section aria-labelledby="securite">
        <h2 id="securite">Sécurité</h2>
        <p>Les formulaires sont protégés contre les envois frauduleux, l'accès à l'administration est réservé au personnel autorisé et les mots de passe sont chiffrés.</p>
    </section>
</main>
