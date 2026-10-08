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
            BE CHILL – Salon de massage<br>
            Rue de la Détente 10<br>
            1000 Bruxelles<br>
            Téléphone : <a href="tel:+32470000000">+32&nbsp;470&nbsp;00&nbsp;00&nbsp;00</a><br>
            Courriel : <a href="mailto:info@bechill.be">info@bechill.be</a>
        </address>
    </section>
    <nav aria-label="Navigation secondaire">
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/soins">Nos soins</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>
    <p><small>&copy; 2026 BE CHILL – Tous droits réservés</small></p>
</footer>

</body>
</html>
