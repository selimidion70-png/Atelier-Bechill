<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'BE CHILL') ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<header>
    <p>BE CHILL</p>
    <p>Salon de massage et bien-être à Bruxelles</p>
</header>

<nav aria-label="Navigation principale">
    <ul>
        <li><a href="/">Accueil</a></li>
        <li><a href="/soins">Nos soins</a></li>
        <li><a href="/tarifs.html">Tarifs</a></li>
        <li><a href="/reservation.html">Réservation</a></li>
        <li><a href="/apropos.html">À propos</a></li>
        <li><a href="/contact">Contact</a></li>
    </ul>
    <form action="/soins" method="get" role="search">
        <label for="recherche-nav">Rechercher un soin</label>
        <input type="search" id="recherche-nav" name="recherche"
               placeholder="Ex : relaxant, sportif…"
               value="<?= htmlspecialchars($_GET['recherche'] ?? '') ?>">
        <button type="submit">Rechercher</button>
    </form>
</nav>

<?= $page_content ?>

<footer>
    <section aria-labelledby="coordonnees-footer">
        <h2 id="coordonnees-footer">Coordonnées</h2>
        <address>
            BE CHILL – Salon de massage<br>
            Rue de la Détente 10<br>
            1000 Bruxelles<br>
            Téléphone : <a href="tel:+32470000000">+32 470 00 00 00</a><br>
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
