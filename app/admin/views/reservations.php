<main>
    <h1>Réservations</h1>

    <form action="/admin/reservations" method="get">
        <fieldset>
            <legend>Filtrer les réservations</legend>
            <p>
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="">Tous</option>
                    <?php foreach ($statuts as $valeur => $libelle): ?>
                        <option value="<?= $valeur ?>" <?= $statut === $valeur ? 'selected' : '' ?>><?= $libelle ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <button type="submit">Filtrer</button>
                <a href="/admin/reservations">Réinitialiser</a>
            </p>
        </fieldset>
    </form>

    <?php if (empty($reservations)): ?>
        <p role="status">Aucune réservation trouvée.</p>
    <?php else: ?>
        <table>
            <caption>Demandes de réservation (triées par date de rendez-vous)</caption>
            <thead>
                <tr>
                    <th scope="col">Rendez-vous</th>
                    <th scope="col">Client</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Soin</th>
                    <th scope="col">Remarques</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Paiement</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($r['date_rdv'])) ?> à <?= substr($r['heure_rdv'], 0, 5) ?></td>
                        <td><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a>
                            <?php if ($r['telephone']): ?>
                                <br><a href="tel:<?= htmlspecialchars($r['telephone']) ?>"><?= htmlspecialchars($r['telephone']) ?></a>
                            <?php endif; ?>
                            <br><small>Préfère : <?= $r['preference_contact'] === 'telephone' ? 'téléphone' : 'courriel' ?></small>
                        </td>
                        <td><?= $r['soin'] !== null ? htmlspecialchars($r['soin']) . ' – ' . (int)$r['duree'] . ' min' : '<em>Soin supprimé</em>' ?></td>
                        <td><?= $r['remarques'] !== null ? nl2br(htmlspecialchars($r['remarques'])) : '' ?></td>
                        <td><?= $statuts[$r['statut']] ?></td>
                        <td>
                            <?= number_format((float)$r['montant'], 2, ',', ' ') ?> €<br>
                            <?php if ($r['paiement'] === 'payee'): ?>
                                Payée le <?= date('d/m/Y', strtotime($r['date_paiement'])) ?>
                            <?php else: ?>
                                Non payée
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php foreach (['confirmee' => 'Confirmer', 'annulee' => 'Annuler', 'en_attente' => 'Remettre en attente'] as $nouveau => $bouton): ?>
                                <?php if ($r['statut'] !== $nouveau): ?>
                                    <form action="/admin/reservations" method="post" style="display:inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="statut_id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="statut" value="<?= $nouveau ?>">
                                        <button type="submit"><?= $bouton ?></button>
                                    </form>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <form action="/admin/reservations" method="post" style="display:inline"
                                  onsubmit="return confirm('Supprimer cette réservation ?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="supprimer_id" value="<?= $r['id'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
