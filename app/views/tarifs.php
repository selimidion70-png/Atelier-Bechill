<main>
    <h1>Nos tarifs</h1>
    <p>Retrouvez ci-dessous les tarifs de l'ensemble de nos soins. Tous les prix sont indiqués TTC.</p>

    <?php if (empty($soinsParCategorie)): ?>
        <p role="status">Aucun soin disponible pour le moment.</p>
    <?php endif; ?>

    <?php foreach ($soinsParCategorie as $categorie => $soins): ?>
        <?php $idSection = 'tarifs-' . item_slug($categorie); ?>
        <section aria-labelledby="<?= $idSection ?>">
            <h2 id="<?= $idSection ?>"><?= htmlspecialchars($categorie) ?></h2>
            <table>
                <caption>Tarifs – <?= htmlspecialchars($categorie) ?></caption>
                <thead>
                    <tr>
                        <th scope="col">Soin</th>
                        <th scope="col">Durée</th>
                        <th scope="col">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($soins as $soin): ?>
                        <tr>
                            <td><a href="/soin-detail?slug=<?= urlencode($soin['slug']) ?>"><?= htmlspecialchars($soin['titre']) ?></a></td>
                            <td><?= (int)$soin['duree'] ?> min</td>
                            <td><?= number_format((float)$soin['prix'], 2, ',', ' ') ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endforeach; ?>

    <section aria-labelledby="formules">
        <h2 id="formules">Formules et abonnements</h2>
        <table>
            <caption>Tarifs des formules et abonnements</caption>
            <thead>
                <tr>
                    <th scope="col">Formule</th>
                    <th scope="col">Détail</th>
                    <th scope="col">Prix</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Carte 5 séances</td><td>5 massages au choix (60 min)</td><td>290 €</td></tr>
                <tr><td>Carte 10 séances</td><td>10 massages au choix (60 min)</td><td>550 €</td></tr>
                <tr><td>Bon cadeau</td><td>Montant au choix, valable 1 an</td><td>À partir de 40 €</td></tr>
            </tbody>
        </table>
    </section>

    <aside aria-labelledby="infos-tarifs">
        <h2 id="infos-tarifs">Informations pratiques</h2>
        <ul>
            <li>Paiement par carte bancaire, Bancontact ou espèces.</li>
            <li>Annulation gratuite jusqu'à 24h avant le rendez-vous.</li>
            <li>Les bons cadeaux sont disponibles à l'accueil et en ligne.</li>
        </ul>
    </aside>
</main>
