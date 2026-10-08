<main>
    <h1>Bienvenue chez BE CHILL</h1>
    <p>Offrez-vous un moment de détente dans notre salon de massage au cœur de Bruxelles.</p>

    <section aria-labelledby="soins-vedettes">
        <h2 id="soins-vedettes">Nos soins vedettes</h2>
        <?php if (empty($soinsVedettes)): ?>
            <p>Aucun soin disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($soinsVedettes as $soin): ?>
                <article>
                    <?php if ($soin['image']): ?>
                        <img class="soin-photo" src="<?= item_image_url($soin['image']) ?>" alt="" loading="lazy">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($soin['titre']) ?></h3>
                    <p><?= htmlspecialchars($soin['description_courte']) ?></p>
                    <p><a href="/soin-detail?slug=<?= urlencode($soin['slug']) ?>">Voir le détail</a></p>
                    <p><a href="/reservation?soin=<?= (int)$soin['id'] ?>">Réserver</a></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <?php if (!empty($collections)): ?>
        <section aria-labelledby="nos-selections">
            <h2 id="nos-selections">Nos sélections</h2>
            <?php foreach ($collections as $c): ?>
                <article>
                    <h3><?= htmlspecialchars($c['nom']) ?></h3>
                    <ul>
                        <?php foreach ($c['soins'] as $s): ?>
                            <li>
                                <a href="/soin-detail?slug=<?= urlencode($s['slug']) ?>"><?= htmlspecialchars($s['titre']) ?></a>
                                – <?= (int)$s['duree'] ?> min, <?= number_format((float)$s['prix'], 2, ',', ' ') ?> €
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section aria-labelledby="pourquoi-nous">
        <h2 id="pourquoi-nous">Pourquoi choisir BE CHILL ?</h2>
        <ul>
            <li>Thérapeutes certifiés et expérimentés</li>
            <li>Produits naturels et biologiques</li>
            <li>Cadre calme et apaisant</li>
            <li>Soins adaptés à vos besoins</li>
        </ul>
        <p><a href="/apropos">En savoir plus sur notre équipe</a></p>
    </section>

    <section aria-labelledby="horaires">
        <h2 id="horaires">Horaires d'ouverture</h2>
        <table>
            <caption>Horaires d'ouverture de BE CHILL</caption>
            <thead>
                <tr>
                    <th scope="col">Jour</th>
                    <th scope="col">Horaire</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Lundi – Vendredi</td><td>9h00 – 19h00</td></tr>
                <tr><td>Samedi</td><td>10h00 – 17h00</td></tr>
                <tr><td>Dimanche</td><td>Fermé</td></tr>
            </tbody>
        </table>
    </section>
</main>
