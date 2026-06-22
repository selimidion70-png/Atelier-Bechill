<main>
    <h1>Nos soins</h1>
    <p>Découvrez notre gamme de massages et soins bien-être.</p>

    <section aria-labelledby="filtres-catalogue">
        <h2 id="filtres-catalogue">Rechercher un soin</h2>
        <form action="/Atelier-Bechill/soins" method="get">
            <fieldset>
                <legend>Filtrer le catalogue</legend>

                <p>
                    <label for="recherche-soin">Rechercher par mot-clé</label><br>
                    <input type="search" id="recherche-soin" name="recherche"
                           placeholder="Ex : relaxant, sportif"
                           value="<?= htmlspecialchars($filtres['recherche']) ?>">
                </p>

                <p>
                    <label for="filtre-categorie">Catégorie</label><br>
                    <select id="filtre-categorie" name="categorie">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ((string)$filtres['categorie'] === (string)$cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="filtre-theme">Thème</label><br>
                    <select id="filtre-theme" name="theme">
                        <option value="">Tous les thèmes</option>
                        <?php foreach ($themes as $th): ?>
                            <option value="<?= $th['id'] ?>"
                                <?= ((string)$filtres['theme'] === (string)$th['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($th['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="filtre-duree">Durée maximale</label><br>
                    <select id="filtre-duree" name="duree">
                        <option value="">Toutes les durées</option>
                        <option value="30" <?= $filtres['duree'] === '30' ? 'selected' : '' ?>>30 min</option>
                        <option value="45" <?= $filtres['duree'] === '45' ? 'selected' : '' ?>>45 min</option>
                        <option value="60" <?= $filtres['duree'] === '60' ? 'selected' : '' ?>>60 min</option>
                        <option value="90" <?= $filtres['duree'] === '90' ? 'selected' : '' ?>>90 min</option>
                    </select>
                </p>

                <p>
                    <button type="submit">Rechercher</button>
                    <a href="/Atelier-Bechill/soins">Réinitialiser</a>
                </p>
            </fieldset>
        </form>
    </section>

    <?php if (empty($soins)): ?>
        <p>Aucun soin ne correspond à votre recherche.</p>
    <?php else: ?>
        <?php foreach ($soinsParCategorie as $nomCategorie => $soinsCategorie): ?>
            <section aria-labelledby="cat-<?= md5($nomCategorie) ?>">
                <h2 id="cat-<?= md5($nomCategorie) ?>"><?= htmlspecialchars($nomCategorie) ?></h2>
                <?php foreach ($soinsCategorie as $soin): ?>
                    <article>
                        <h3><?= htmlspecialchars($soin['titre']) ?></h3>
                        <p><strong>Durée :</strong> <?= (int)$soin['duree'] ?> min</p>
                        <p><strong>Prix :</strong> <?= number_format((float)$soin['prix'], 2, ',', ' ') ?> €</p>
                        <p><?= htmlspecialchars($soin['description_courte']) ?></p>
                        <p><a href="/Atelier-Bechill/soin-detail?slug=<?= urlencode($soin['slug']) ?>">Voir le détail</a></p>
                        <p><a href="/Atelier-Bechill/reservation.html">Réserver</a></p>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
