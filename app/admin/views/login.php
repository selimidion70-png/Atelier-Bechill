<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Connexion') ?></title>
    <link rel="stylesheet" href="/Atelier-Bechill/style.css">
</head>
<body>

<header>
    <p>BE CHILL – Administration</p>
</header>

<main>
    <h1>Connexion à l'espace d'administration</h1>

    <?php if ($erreur !== ''): ?>
        <p role="alert" style="color:red;"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form action="/Atelier-Bechill/admin/login" method="post">
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
    <p><a href="/Atelier-Bechill/">Retour au site public</a></p>
</main>

<footer>
    <p><small>&copy; 2026 BE CHILL</small></p>
</footer>

</body>
</html>
