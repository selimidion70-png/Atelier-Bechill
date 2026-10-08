<?php
// Champs communs aux formulaires « Ajouter » et « Modifier » un soin
// Variables attendues : $valeurs, $categories, $themes, $tagsDisponibles
?>
        <fieldset>
            <legend>Informations générales</legend>
            <p>
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" required maxlength="150"
                       value="<?= htmlspecialchars($valeurs['titre']) ?>">
            </p>
            <p>
                <label for="description_courte">Description courte</label>
                <textarea id="description_courte" name="description_courte" rows="2" required maxlength="255"><?= htmlspecialchars($valeurs['description_courte']) ?></textarea>
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
                    <option value="archive"   <?= $valeurs['statut'] === 'archive'   ? 'selected' : '' ?>>Archivé</option>
                </select>
            </p>
        </fieldset>

        <fieldset>
            <legend>Tags</legend>
            <?php if (empty($tagsDisponibles)): ?>
                <p>Aucun tag. <a href="/admin/tags">Créer des tags</a></p>
            <?php endif; ?>
            <?php foreach ($tagsDisponibles as $tag): ?>
                <p>
                    <input type="checkbox" id="tag-<?= $tag['id'] ?>" name="tags[]" value="<?= $tag['id'] ?>"
                           <?= in_array((string)$tag['id'], array_map('strval', $valeurs['tags']), true) ? 'checked' : '' ?>>
                    <label for="tag-<?= $tag['id'] ?>"><?= htmlspecialchars($tag['nom']) ?></label>
                </p>
            <?php endforeach; ?>
        </fieldset>
