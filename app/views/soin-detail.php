<main>
    <nav aria-label="Fil d'Ariane">
        <ol>
            <li><a href="/">Accueil</a></li>
            <li><a href="/soins">Nos soins</a></li>
            <li aria-current="page"><?= htmlspecialchars($soin['titre']) ?></li>
        </ol>
    </nav>

    <article>
        <h1><?= htmlspecialchars($soin['titre']) ?></h1>

        <section aria-labelledby="description-soin">
            <h2 id="description-soin">Description</h2>
            <p><?= nl2br(htmlspecialchars($soin['description'])) ?></p>
        </section>

        <section aria-labelledby="details-pratiques">
            <h2 id="details-pratiques">Détails pratiques</h2>
            <dl>
                <dt>Durée</dt>
                <dd><?= (int)$soin['duree'] ?> minutes</dd>

                <dt>Prix</dt>
                <dd><?= number_format((float)$soin['prix'], 2, ',', ' ') ?> €</dd>

                <dt>Catégorie</dt>
                <dd><?= htmlspecialchars($soin['categorie']) ?></dd>

                <dt>Thème</dt>
                <dd><?= htmlspecialchars($soin['theme']) ?></dd>

                <?php if (!empty($tags)): ?>
                    <dt>Mots-clés</dt>
                    <dd><?= htmlspecialchars(implode(', ', $tags)) ?></dd>
                <?php endif; ?>
            </dl>
        </section>

        <p><a href="/reservation?soin=<?= (int)$soin['id'] ?>">Réserver ce soin</a></p>
        <p><a href="/soins">Retour à la liste</a></p>
    </article>
</main>
