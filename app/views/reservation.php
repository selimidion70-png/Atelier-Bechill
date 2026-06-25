<main>
    <h1>Réserver un soin</h1>
    <p>Remplissez le formulaire ci-dessous pour réserver votre séance. Nous vous confirmerons votre rendez-vous par courriel ou par téléphone.</p>

    <?php if ($succes): ?>
        <p role="status">Votre réservation a bien été envoyée. Nous vous contacterons rapidement pour confirmer votre rendez-vous.</p>
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

    <?php if (!$succes): ?>
    <form action="/reservation" method="post">
        <fieldset>
            <legend>Vos coordonnées</legend>
            <p>
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" autocomplete="family-name" required>
            </p>
            <p>
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" autocomplete="given-name" required>
            </p>
            <p>
                <label for="email">Adresse courriel</label>
                <input type="email" id="email" name="email" autocomplete="email" required>
            </p>
            <p>
                <label for="telephone">Numéro de téléphone</label>
                <input type="tel" id="telephone" name="telephone" autocomplete="tel">
            </p>
        </fieldset>

        <fieldset>
            <legend>Votre réservation</legend>
            <p>
                <label for="soin">Soin souhaité</label>
                <select id="soin" name="soin" required>
                    <option value="">-- Choisissez un soin --</option>
                    <optgroup label="Massages classiques">
                        <option value="relaxant-60">Massage relaxant – 60 min</option>
                        <option value="suedois-60">Massage suédois – 60 min</option>
                        <option value="suedois-90">Massage suédois – 90 min</option>
                        <option value="pierres-75">Massage aux pierres chaudes – 75 min</option>
                    </optgroup>
                    <optgroup label="Massages spécifiques">
                        <option value="sportif-60">Massage sportif – 60 min</option>
                        <option value="sportif-90">Massage sportif – 90 min</option>
                        <option value="dos-nuque-30">Massage dos et nuque – 30 min</option>
                        <option value="reflexologie-45">Réflexologie plantaire – 45 min</option>
                    </optgroup>
                </select>
            </p>
            <p>
                <label for="date">Date souhaitée</label>
                <input type="date" id="date" name="date" required>
            </p>
            <p>
                <label for="heure">Heure souhaitée</label>
                <select id="heure" name="heure" required>
                    <option value="">-- Choisissez une heure --</option>
                    <option value="09:00">09h00</option>
                    <option value="10:00">10h00</option>
                    <option value="11:00">11h00</option>
                    <option value="13:00">13h00</option>
                    <option value="14:00">14h00</option>
                    <option value="15:00">15h00</option>
                    <option value="16:00">16h00</option>
                    <option value="17:00">17h00</option>
                    <option value="18:00">18h00</option>
                </select>
            </p>
        </fieldset>

        <fieldset>
            <legend>Préférence de contact</legend>
            <p>
                <input type="radio" id="contact-email" name="preference-contact" value="email" checked>
                <label for="contact-email">Par courriel</label>
            </p>
            <p>
                <input type="radio" id="contact-telephone" name="preference-contact" value="telephone">
                <label for="contact-telephone">Par téléphone</label>
            </p>
        </fieldset>

        <fieldset>
            <legend>Informations complémentaires</legend>
            <p>
                <label for="remarques">Remarques ou demandes particulières</label>
                <textarea id="remarques" name="remarques" rows="5"></textarea>
            </p>
            <p>
                <input type="checkbox" id="conditions" name="conditions" required>
                <label for="conditions">J'accepte les conditions d'annulation (annulation gratuite jusqu'à 24h avant le rendez-vous)</label>
            </p>
        </fieldset>

        <p>
            <button type="submit">Envoyer ma réservation</button>
            <button type="reset">Réinitialiser</button>
        </p>
    </form>
    <?php endif; ?>
</main>
