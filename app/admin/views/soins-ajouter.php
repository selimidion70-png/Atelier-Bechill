<main>
    <nav aria-label="Fil d'Ariane">
        <ol>
            <li><a href="/admin">Tableau de bord</a></li>
            <li><a href="/admin/soins">Soins</a></li>
            <li aria-current="page">Ajouter un soin</li>
        </ol>
    </nav>

    <h1>Ajouter un soin</h1>

    <?php if ($succes): ?>
        <p role="status">Le soin a bien été ajouté. <a href="/admin/soins">Retour à la liste</a></p>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
        <div role="alert">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/admin/soins-ajouter" method="post">
        <fieldset>
            <legend>Informations générales</legend>
            <p>
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" required
                       value="<?= htmlspecialchars($valeurs['titre']) ?>">
            </p>
            <p>
                <label for="description_courte">Description courte</label>
                <textarea id="description_courte" name="description_courte" rows="2" required><?= htmlspecialchars($valeurs['description_courte']) ?></textarea>
            </p>
            <p>
                <label for="description">Description complète</label>
                <textarea id="description" name="description" rows="6" required><?= htmlspecialchars($valeurs['description']) ?></textarea>
            </p>
        </fieldset>

        <fieldset>
            <legend>Détails pratiques</legend>
            <p>
                <label for="duree">Durée (en minutes)</label>
                <input type="number" id="duree" name="duree" min="1" required
                       value="<?= htmlspecialchars($valeurs['duree']) ?>">
            </p>
            <p>
                <label for="prix">Prix (€)</label>
                <input type="number" id="prix" name="prix" min="0" step="0.01" required
                       value="<?= htmlspecialchars($valeurs['prix']) ?>">
            </p>
            <p>
                <label for="category_id">Catégorie</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Choisissez une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (string)$valeurs['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label for="theme_id">Thème</label>
                <select id="theme_id" name="theme_id" required>
                    <option value="">-- Choisissez un thème --</option>
                    <?php foreach ($themes as $th): ?>
                        <option value="<?= $th['id'] ?>"
                            <?= (string)$valeurs['theme_id'] === (string)$th['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($th['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="brouillon" <?= $valeurs['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                    <option value="publie"    <?= $valeurs['statut'] === 'publie'    ? 'selected' : '' ?>>Publié</option>
                </select>
            </p>
        </fieldset>

        <p>
            <button type="submit">Enregistrer le soin</button>
            <a href="/admin/soins">Annuler</a>
        </p>
    </form>
</main>
