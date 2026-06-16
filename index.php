<?php
require_once __DIR__ . '/db.php';

// 3 soins mis en avant (les plus récents publiés)
$stmt = $pdo->query("
    SELECT titre, slug, description_courte
    FROM item
    WHERE statut = 'publie'
    ORDER BY date_creation DESC
    LIMIT 3
");
$soinsVedettes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BE CHILL – Salon de massage à Bruxelles</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>BE CHILL</h1>
        <p>Salon de massage et bien-être à Bruxelles</p>
    </header>

    <nav aria-label="Navigation principale">
        <ul>
            <li><a href="index.php" aria-current="page">Accueil</a></li>
            <li><a href="soins.php">Nos soins</a></li>
            <li><a href="tarifs.html">Tarifs</a></li>
            <li><a href="reservation.html">Réservation</a></li>
            <li><a href="apropos.html">À propos</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <form action="soins.php" method="get" role="search" aria-label="Recherche de soins">
            <label for="recherche-nav">Rechercher un soin</label>
            <input type="search" id="recherche-nav" name="recherche" placeholder="Ex : relaxant, sportif…">
            <button type="submit">Rechercher</button>
        </form>
    </nav>

    <main>
        <h2>Bienvenue chez BE CHILL</h2>
        <p>Offrez-vous un moment de détente dans notre salon de massage au cœur de Bruxelles. Nos thérapeutes qualifiés vous accueillent dans un cadre apaisant pour des soins personnalisés.</p>

        <section aria-labelledby="soins-vedettes">
            <h2 id="soins-vedettes">Nos soins vedettes</h2>

            <?php if (empty($soinsVedettes)): ?>
                <p>Aucun soin n'est actuellement disponible.</p>
            <?php else: ?>
                <?php foreach ($soinsVedettes as $soin): ?>
                    <article>
                        <h3><?= htmlspecialchars($soin['titre']) ?></h3>
                        <p><?= htmlspecialchars($soin['description_courte']) ?></p>
                        <p><a href="soin-detail.php?slug=<?= urlencode($soin['slug']) ?>">Voir le détail</a></p>
                        <p><a href="reservation.html">Réserver <?= htmlspecialchars(mb_strtolower($soin['titre'])) ?></a></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section aria-labelledby="pourquoi-nous">
            <h2 id="pourquoi-nous">Pourquoi choisir BE CHILL ?</h2>
            <ul>
                <li>Thérapeutes certifiés et expérimentés</li>
                <li>Produits naturels et biologiques</li>
                <li>Cadre calme et apaisant</li>
                <li>Soins adaptés à vos besoins</li>
            </ul>
            <p><a href="apropos.html">En savoir plus sur notre équipe</a></p>
        </section>

        <section aria-labelledby="horaires-accueil">
            <h2 id="horaires-accueil">Horaires d'ouverture</h2>
            <table>
                <caption>Horaires d'ouverture de BE CHILL</caption>
                <thead>
                    <tr>
                        <th scope="col">Jour</th>
                        <th scope="col">Horaire</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Lundi – Vendredi</td>
                        <td>9h00 – 19h00</td>
                    </tr>
                    <tr>
                        <td>Samedi</td>
                        <td>10h00 – 17h00</td>
                    </tr>
                    <tr>
                        <td>Dimanche</td>
                        <td>Fermé</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <section aria-labelledby="coordonnees">
            <h2 id="coordonnees">Coordonnées</h2>
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
                <li><a href="index.php">Accueil</a></li>
                <li><a href="soins.php">Nos soins</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
        <p><small>&copy; 2026 BE CHILL – Tous droits réservés</small></p>
    </footer>

</body>
</html>
