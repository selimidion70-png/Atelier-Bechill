<?php $montant = number_format((float)$reservation['montant'], 2, ',', ' ') . ' €'; ?>
<main class="paiement">
    <?php if ($etape === 'payee'): ?>
        <h1>Merci, votre réservation est payée</h1>
        <p role="status">Le paiement de <strong><?= $montant ?></strong> a bien été enregistré. Nous vous contacterons rapidement pour confirmer votre rendez-vous.</p>
    <?php elseif ($etape === 'sur_place'): ?>
        <h1>Votre réservation est enregistrée</h1>
        <p role="status">Vous réglerez <strong><?= $montant ?></strong> sur place, le jour du rendez-vous. Nous vous contacterons rapidement pour confirmer votre rendez-vous.</p>
    <?php else: ?>
        <h1>Paiement de votre réservation</h1>
        <p>Votre demande est enregistrée. Vous pouvez la régler maintenant en ligne ou sur place.</p>
    <?php endif; ?>

    <section aria-labelledby="recapitulatif" class="paiement__recap">
        <h2 id="recapitulatif">Récapitulatif</h2>
        <table>
            <caption>Détail de votre réservation</caption>
            <tbody>
                <tr><th scope="row">Soin</th><td><?= htmlspecialchars($reservation['soin'] ?? '') ?> – <?= (int)$reservation['duree'] ?> min</td></tr>
                <tr><th scope="row">Rendez-vous</th><td><?= date('d/m/Y', strtotime($reservation['date_rdv'])) ?> à <?= substr($reservation['heure_rdv'], 0, 5) ?></td></tr>
                <tr><th scope="row">Au nom de</th><td><?= htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']) ?></td></tr>
                <tr><th scope="row">Montant</th><td><strong><?= $montant ?></strong></td></tr>
            </tbody>
        </table>
    </section>

    <?php if ($etape === 'choix'): ?>
        <section aria-labelledby="moyens-paiement">
            <h2 id="moyens-paiement">Moyens de paiement acceptés</h2>
            <ul class="paiement__cartes">
                <li>Bancontact</li>
                <li>Visa</li>
                <li>Mastercard</li>
                <li>Maestro</li>
                <li>Revolut</li>
                <li>Apple Pay</li>
            </ul>

            <p class="paiement__demo" role="note">
                <strong>Démonstration :</strong> ce paiement est simulé. Aucune donnée de carte n'est demandée et aucun montant n'est débité.
            </p>

            <form action="/paiement" method="post" class="paiement__actions">
                <?= csrf_field() ?>
                <button type="submit" name="choix" value="payer">Payer <?= $montant ?></button>
                <button type="submit" name="choix" value="sur_place">Payer sur place</button>
            </form>
        </section>
    <?php else: ?>
        <p><a href="/">Retour à l'accueil</a> · <a href="/soins">Découvrir nos autres soins</a></p>
    <?php endif; ?>
</main>
