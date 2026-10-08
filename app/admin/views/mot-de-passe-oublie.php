<main>
    <h1>Mot de passe oublié</h1>

    <?php if ($envoye): ?>
        <p role="status">Si cette adresse correspond à un compte, un email avec un lien de réinitialisation vient d'être envoyé. Le lien est valable 1 heure.</p>
    <?php else: ?>
        <p>Indiquez l'adresse email de votre compte : vous recevrez un lien pour choisir un nouveau mot de passe.</p>

        <form action="/admin/mot-de-passe-oublie" method="post">
            <?= csrf_field() ?>
            <p>
                <label for="email">Adresse email</label><br>
                <input type="email" id="email" name="email" autocomplete="username" required>
            </p>
            <p><button type="submit">Envoyer le lien</button></p>
        </form>
    <?php endif; ?>

    <p><a href="/admin/login">Retour à la connexion</a></p>
</main>
