<main>
    <h1>Connexion à l'espace d'administration</h1>

    <?php if ($erreur !== ''): ?>
        <p role="alert" style="color:red;"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form action="/admin/login" method="post">
        <?= csrf_field() ?>
        <fieldset>
            <legend>Identifiants de connexion</legend>
            <p>
                <label for="identifiant">Identifiant (email)</label><br>
                <input type="text" id="identifiant" name="identifiant" autocomplete="username" required>
            </p>
            <p>
                <label for="mot-de-passe">Mot de passe</label><br>
                <input type="password" id="mot-de-passe" name="mot-de-passe" autocomplete="current-password" required>
            </p>
        </fieldset>
        <p>
            <button type="submit">Se connecter</button>
        </p>
    </form>
    <p><a href="/admin/mot-de-passe-oublie">Mot de passe oublié ?</a></p>
    <p><a href="/">Retour au site public</a></p>
</main>
