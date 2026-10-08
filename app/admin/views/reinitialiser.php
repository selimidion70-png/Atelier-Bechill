<main>
    <h1>Choisir un nouveau mot de passe</h1>

    <?php if ($termine): ?>
        <p role="status">Votre mot de passe a été modifié. Vous pouvez maintenant vous connecter.</p>
        <p><a href="/admin/login">Se connecter</a></p>

    <?php elseif (!$valide): ?>
        <div role="alert">
            <p>Ce lien n'est plus valable : il a expiré (1 heure) ou a déjà été utilisé.</p>
        </div>
        <p><a href="/admin/mot-de-passe-oublie">Demander un nouveau lien</a></p>

    <?php else: ?>
        <?php if (!empty($erreurs)): ?>
            <div role="alert">
                <ul>
                    <?php foreach ($erreurs as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/admin/reinitialiser?jeton=<?= htmlspecialchars($jeton) ?>" method="post">
            <?= csrf_field() ?>
            <p>
                <label for="mot_de_passe">Nouveau mot de passe (<?= OPERATOR_MDP_MIN ?> caractères minimum)</label><br>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="<?= OPERATOR_MDP_MIN ?>" autocomplete="new-password">
            </p>
            <p>
                <label for="confirmation">Confirmer le mot de passe</label><br>
                <input type="password" id="confirmation" name="confirmation" required minlength="<?= OPERATOR_MDP_MIN ?>" autocomplete="new-password">
            </p>
            <p><button type="submit">Enregistrer</button></p>
        </form>
    <?php endif; ?>
</main>
