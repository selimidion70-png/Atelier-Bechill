<main>
    <nav aria-label="Fil d'Ariane">
        <ol>
            <li><a href="/admin">Tableau de bord</a></li>
            <li><a href="/admin/collections">Collections</a></li>
            <li aria-current="page"><?= $creation ? 'Nouvelle collection' : 'Modifier' ?></li>
        </ol>
    </nav>

    <h1><?= $creation ? 'Nouvelle collection' : 'Modifier la collection' ?></h1>

    <?php if (!empty($erreurs)): ?>
        <div role="alert">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/admin/collections-modifier<?= $creation ? '' : '?id=' . $id ?>" method="post">
        <?= csrf_field() ?>
        <p>
            <label for="nom">Nom de la collection</label>
            <input type="text" id="nom" name="nom" required maxlength="150"
                   value="<?= htmlspecialchars($valeurs['nom']) ?>">
        </p>

        <fieldset>
            <legend>Soins de la collection</legend>
            <p><small>Seuls les soins publiés apparaissent sur le site.</small></p>
            <?php foreach ($soins as $soin): ?>
                <p>
                    <input type="checkbox" id="soin-<?= $soin['id'] ?>" name="soins[]" value="<?= $soin['id'] ?>"
                           <?= in_array((int)$soin['id'], $valeurs['soins'], true) ? 'checked' : '' ?>>
                    <label for="soin-<?= $soin['id'] ?>">
                        <?= htmlspecialchars($soin['titre']) ?>
                        <?php if ($soin['statut'] !== 'publie'): ?><small>(<?= htmlspecialchars($soin['statut']) ?>)</small><?php endif; ?>
                    </label>
                </p>
            <?php endforeach; ?>
        </fieldset>

        <p>
            <button type="submit">Enregistrer</button>
            <a href="/admin/collections">Annuler</a>
        </p>
    </form>
</main>
