<main>
    <h1>Messages reçus</h1>

    <form action="/admin/messages" method="get">
        <fieldset>
            <legend>Filtrer les messages</legend>
            <p>
                <label for="recherche">Rechercher</label>
                <input type="search" id="recherche" name="recherche"
                       value="<?= htmlspecialchars($filtres['recherche']) ?>"
                       placeholder="Nom, sujet…">
            </p>
            <p>
                <label for="sujet">Sujet</label>
                <select id="sujet" name="sujet">
                    <option value="">Tous les sujets</option>
                    <option value="information"  <?= $filtres['sujet'] === 'information'  ? 'selected' : '' ?>>Demande d'information</option>
                    <option value="reservation"  <?= $filtres['sujet'] === 'reservation'  ? 'selected' : '' ?>>Question sur une réservation</option>
                    <option value="reclamation"  <?= $filtres['sujet'] === 'reclamation'  ? 'selected' : '' ?>>Réclamation</option>
                    <option value="partenariat"  <?= $filtres['sujet'] === 'partenariat'  ? 'selected' : '' ?>>Partenariat</option>
                    <option value="autre"        <?= $filtres['sujet'] === 'autre'        ? 'selected' : '' ?>>Autre</option>
                </select>
            </p>
            <p>
                <label for="lu">Statut</label>
                <select id="lu" name="lu">
                    <option value="">Tous</option>
                    <option value="non-lu" <?= $filtres['lu'] === 'non-lu' ? 'selected' : '' ?>>Non lus</option>
                    <option value="lu"     <?= $filtres['lu'] === 'lu'     ? 'selected' : '' ?>>Lus</option>
                </select>
            </p>
            <p>
                <button type="submit">Filtrer</button>
                <a href="/admin/messages">Réinitialiser</a>
            </p>
        </fieldset>
    </form>

    <?php if (empty($messages)): ?>
        <p role="status">Aucun message trouvé.</p>
    <?php else: ?>
        <table>
            <caption>Liste des messages de contact</caption>
            <thead>
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Courriel</th>
                    <th scope="col">Sujet</th>
                    <th scope="col">Message</th>
                    <th scope="col">Lu</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($msg['date_envoi'])) ?></td>
                        <td><?= htmlspecialchars($msg['nom']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a></td>
                        <td><?= htmlspecialchars(ucfirst($msg['sujet'])) ?></td>
                        <td><?= nl2br(htmlspecialchars(mb_substr($msg['texte'], 0, 80)), false) ?>…</td>
                        <td><?= $msg['lu'] ? 'Oui' : 'Non' ?></td>
                        <td>
                            <form action="/admin/messages" method="post" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="lu_id" value="<?= $msg['id'] ?>">
                                <?php if ($msg['lu']): ?>
                                    <input type="hidden" name="lu" value="0">
                                    <button type="submit">Marquer non lu</button>
                                <?php else: ?>
                                    <input type="hidden" name="lu" value="1">
                                    <button type="submit">Marquer lu</button>
                                <?php endif; ?>
                            </form>
                            <form action="/admin/messages" method="post" style="display:inline"
                                  onsubmit="return confirm('Supprimer ce message ?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="supprimer_id" value="<?= $msg['id'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
