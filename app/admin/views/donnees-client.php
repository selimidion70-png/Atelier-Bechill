<main>
    <h1>Données d'un client (RGPD)</h1>
    <p>
        Quand un client demande à voir ou à supprimer ses données, cherchez son adresse email ici.
        Les données anciennes sont aussi supprimées automatiquement : réservations
        <?= (int)config('rgpd_conservation_reservations') ?> mois après le rendez-vous,
        messages <?= (int)config('rgpd_conservation_messages') ?> mois après leur envoi.
    </p>

    <?php if ($supprime): ?>
        <p role="status">Toutes les données de ce client ont été supprimées.</p>
    <?php endif; ?>

    <form action="/admin/donnees-client" method="get">
        <p>
            <label for="email">Adresse email du client</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
            <button type="submit">Rechercher</button>
        </p>
    </form>

    <?php if ($donnees !== null): ?>
        <?php if (empty($donnees['reservations']) && empty($donnees['messages'])): ?>
            <p role="status">Aucune donnée enregistrée pour <?= htmlspecialchars($email) ?>.</p>
        <?php else: ?>
            <section aria-labelledby="client-reservations">
                <h2 id="client-reservations">Réservations (<?= count($donnees['reservations']) ?>)</h2>
                <?php if (!empty($donnees['reservations'])): ?>
                    <table>
                        <caption>Réservations de <?= htmlspecialchars($email) ?></caption>
                        <thead>
                            <tr>
                                <th scope="col">Rendez-vous</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Téléphone</th>
                                <th scope="col">Soin</th>
                                <th scope="col">Remarques</th>
                                <th scope="col">Paiement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donnees['reservations'] as $r): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r['date_rdv'])) ?> à <?= substr($r['heure_rdv'], 0, 5) ?></td>
                                    <td><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></td>
                                    <td><?= htmlspecialchars($r['telephone'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($r['soin'] ?? '—') ?></td>
                                    <td><?= $r['remarques'] !== null ? nl2br(htmlspecialchars($r['remarques']), false) : '' ?></td>
                                    <td><?= $r['paiement'] === 'payee' ? 'Payée' : 'Non payée' ?> (<?= number_format((float)$r['montant'], 2, ',', ' ') ?> €)</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <section aria-labelledby="client-messages">
                <h2 id="client-messages">Messages (<?= count($donnees['messages']) ?>)</h2>
                <?php foreach ($donnees['messages'] as $m): ?>
                    <article>
                        <h3><?= htmlspecialchars(ucfirst($m['sujet'])) ?> – <?= date('d/m/Y H:i', strtotime($m['date_envoi'])) ?></h3>
                        <p><?= nl2br(htmlspecialchars($m['texte']), false) ?></p>
                    </article>
                <?php endforeach; ?>
            </section>

            <form action="/admin/donnees-client" method="post"
                  onsubmit="return confirm('Supprimer définitivement toutes les données de ce client ?')">
                <?= csrf_field() ?>
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <p><button type="submit">Supprimer toutes les données de ce client</button></p>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</main>
