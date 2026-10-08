<main>
    <h1>Réserver un soin</h1>
    <p>Remplissez le formulaire ci-dessous pour réserver votre séance. Nous vous confirmerons votre rendez-vous par courriel ou par téléphone.</p>

    <?php if (!empty($erreurs)): ?>
        <div role="alert">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/reservation" method="post">
        <?= csrf_field() ?>
        <fieldset>
            <legend>Vos coordonnées</legend>
            <p>
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" autocomplete="family-name" required
                       value="<?= htmlspecialchars($valeurs['nom']) ?>">
            </p>
            <p>
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" autocomplete="given-name" required
                       value="<?= htmlspecialchars($valeurs['prenom']) ?>">
            </p>
            <p>
                <label for="email">Adresse courriel</label>
                <input type="email" id="email" name="email" autocomplete="email" required
                       value="<?= htmlspecialchars($valeurs['email']) ?>">
            </p>
            <p>
                <label for="telephone">Numéro de téléphone</label>
                <input type="tel" id="telephone" name="telephone" autocomplete="tel"
                       value="<?= htmlspecialchars($valeurs['telephone']) ?>">
            </p>
        </fieldset>

        <fieldset>
            <legend>Votre réservation</legend>
            <p>
                <label for="soin">Soin souhaité</label>
                <select id="soin" name="soin" required>
                    <option value="">-- Choisissez un soin --</option>
                    <?php foreach ($soinsParCategorie as $categorie => $soins): ?>
                        <optgroup label="<?= htmlspecialchars($categorie) ?>">
                            <?php foreach ($soins as $soin): ?>
                                <option value="<?= $soin['id'] ?>"
                                    <?= (string)$valeurs['soin'] === (string)$soin['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($soin['titre']) ?> – <?= (int)$soin['duree'] ?> min
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label for="date">Date souhaitée</label>
                <input type="date" id="date" name="date" required min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($valeurs['date']) ?>">
            </p>
            <p>
                <label for="heure">Heure souhaitée</label>
                <select id="heure" name="heure" required>
                    <option value="">-- Choisissez une heure --</option>
                    <?php foreach ($heures as $h): ?>
                        <option value="<?= $h ?>" <?= $valeurs['heure'] === $h ? 'selected' : '' ?>><?= str_replace(':', 'h', $h) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        </fieldset>

        <fieldset>
            <legend>Préférence de contact</legend>
            <p>
                <input type="radio" id="contact-email" name="preference-contact" value="email"
                       <?= $valeurs['preference-contact'] !== 'telephone' ? 'checked' : '' ?>>
                <label for="contact-email">Par courriel</label>
            </p>
            <p>
                <input type="radio" id="contact-telephone" name="preference-contact" value="telephone"
                       <?= $valeurs['preference-contact'] === 'telephone' ? 'checked' : '' ?>>
                <label for="contact-telephone">Par téléphone</label>
            </p>
        </fieldset>

        <fieldset>
            <legend>Informations complémentaires</legend>
            <p>
                <label for="remarques">Remarques ou demandes particulières</label>
                <textarea id="remarques" name="remarques" rows="5"><?= htmlspecialchars($valeurs['remarques']) ?></textarea>
            </p>
            <p>
                <input type="checkbox" id="conditions" name="conditions" required>
                <label for="conditions">J'accepte les conditions d'annulation (annulation gratuite jusqu'à 24h avant le rendez-vous)</label>
            </p>
        </fieldset>

        <p>
            <button type="submit">Continuer vers le paiement</button>
            <button type="reset">Réinitialiser</button>
        </p>
    </form>
</main>
