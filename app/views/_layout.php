<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'BE CHILL') ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<header class="site-header">
    <div class="site-header__left">
        <a href="/" class="site-header__brand">BE CHILL</a>
        <p class="site-header__tagline">Salon de massage &amp; bien-être · Bruxelles</p>
    </div>
    <nav class="site-header__nav" aria-label="Navigation principale">
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/soins">Nos soins</a></li>
            <li><a href="/tarifs">Tarifs</a></li>
            <li><a href="/reservation">Réservation</a></li>
            <li><a href="/apropos">À propos</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
        <form action="/soins" method="get" role="search">
            <label for="recherche-nav">Rechercher un soin</label>
            <input type="search" id="recherche-nav" name="recherche"
                   placeholder="Rechercher…"
                   value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>">
            <button type="submit">&#128269;</button>
        </form>
    </nav>
</header>

<?= $page_content ?>

<footer>
    <section aria-labelledby="coordonnees-footer">
        <h2 id="coordonnees-footer">Coordonnées</h2>
        <address>
            <?= htmlspecialchars(salon('nom') . ' – ' . salon('activite')) ?><br>
            <?= htmlspecialchars(salon('adresse')) ?><br>
            <?= htmlspecialchars(salon('code_postal') . ' ' . salon('ville')) ?><br>
            Téléphone : <a href="tel:<?= preg_replace('/[^0-9+]/', '', salon('telephone')) ?>"><?= str_replace(' ', '&nbsp;', htmlspecialchars(salon('telephone'))) ?></a><br>
            Courriel : <a href="mailto:<?= htmlspecialchars(salon('email')) ?>"><?= htmlspecialchars(salon('email')) ?></a>
        </address>
    </section>
    <nav aria-label="Navigation secondaire">
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/soins">Nos soins</a></li>
            <li><a href="/contact">Contact</a></li>
            <li><a href="/mentions-legales">Mentions légales</a></li>
            <li><a href="/confidentialite">Politique de confidentialité</a></li>
        </ul>
    </nav>
    <p><small>&copy; <?= date('Y') ?> <?= htmlspecialchars(salon('nom')) ?> – Tous droits réservés</small></p>
</footer>

</body>
</html>
