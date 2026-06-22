<main>
    <h1>Contactez-nous</h1>

    <?php if ($succes): ?>
        <p role="status">Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.</p>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
        <div role="alert" style="color:red;">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/contact" method="post">
        <fieldset>
            <legend>Vos coordonnées</legend>
            <p>
                <label for="nom">Nom</label><br>
                <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($valeurs['nom']) ?>">
            </p>
            <p>
                <label for="prenom">Prénom</label><br>
                <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($valeurs['prenom']) ?>">
            </p>
            <p>
                <label for="email">Courriel</label><br>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($valeurs['email']) ?>">
            </p>
            <p>
                <label for="telephone">Téléphone</label><br>
                <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($valeurs['telephone']) ?>">
            </p>
        </fieldset>

        <fieldset>
            <legend>Votre message</legend>
            <p>
                <label for="sujet">Sujet</label><br>
                <select id="sujet" name="sujet" required>
                    <option value="">-- Choisissez un sujet --</option>
                    <option value="information" <?= $valeurs['sujet'] === 'information' ? 'selected' : '' ?>>Demande d'information</option>
                    <option value="reservation" <?= $valeurs['sujet'] === 'reservation' ? 'selected' : '' ?>>Question sur une réservation</option>
                    <option value="reclamation" <?= $valeurs['sujet'] === 'reclamation' ? 'selected' : '' ?>>Réclamation</option>
                    <option value="partenariat" <?= $valeurs['sujet'] === 'partenariat' ? 'selected' : '' ?>>Proposition de partenariat</option>
                    <option value="autre" <?= $valeurs['sujet'] === 'autre' ? 'selected' : '' ?>>Autre</option>
                </select>
            </p>
            <p>
                <label for="message">Message</label><br>
                <textarea id="message" name="message" rows="6" required><?= htmlspecialchars($valeurs['message']) ?></textarea>
            </p>
        </fieldset>

        <p>
            <button type="submit">Envoyer</button>
            <button type="reset">Effacer</button>
        </p>
    </form>
</main>
