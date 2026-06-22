<main>
    <h1>Tableau de bord</h1>
    <p>Bienvenue dans l'espace d'administration de BE CHILL.</p>

    <section aria-labelledby="resume">
        <h2 id="resume">Résumé</h2>
        <table>
            <caption>Indicateurs du tableau de bord</caption>
            <thead>
                <tr>
                    <th scope="col">Indicateur</th>
                    <th scope="col">Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Soins publiés</td><td><?= $nbSoinsActifs ?></td></tr>
                <tr><td>Soins au total</td><td><?= $nbSoinsTotal ?></td></tr>
                <tr><td>Messages non lus</td><td><?= $nbMessagesNonLus ?></td></tr>
            </tbody>
        </table>
    </section>

    <section aria-labelledby="acces-rapide">
        <h2 id="acces-rapide">Accès rapide</h2>
        <ul>
            <li><a href="/admin/soins-ajouter">Ajouter un nouveau soin</a></li>
            <li><a href="/admin/messages">Lire les messages</a></li>
            <li><a href="/">Voir le site public</a></li>
        </ul>
    </section>

    <section aria-labelledby="derniers-soins">
        <h2 id="derniers-soins">Derniers soins ajoutés</h2>
        <table>
            <caption>Les 5 derniers soins créés</caption>
            <thead>
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Nom du soin</th>
                    <th scope="col">Catégorie</th>
                    <th scope="col">Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($derniersSoins)): ?>
                    <tr><td colspan="4">Aucun soin enregistré.</td></tr>
                <?php else: ?>
                    <?php foreach ($derniersSoins as $soin): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($soin['date_creation'])) ?></td>
                            <td><?= htmlspecialchars($soin['titre']) ?></td>
                            <td><?= htmlspecialchars($soin['categorie']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($soin['statut'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>
