<?php
require_once __DIR__ . '/db.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
    header('Location: soins.php');
    exit;
}

// Récupération du soin (uniquement s'il est publié)
$stmt = $pdo->prepare("
    SELECT item.*, category.nom AS categorie, theme.nom AS theme
    FROM item
    JOIN category ON item.category_id = category.id
    JOIN theme ON item.theme_id = theme.id
    WHERE item.slug = :slug AND item.statut = 'publie'
");
$stmt->execute(['slug' => $slug]);
$soin = $stmt->fetch();

if (!$soin) {
    header('Location: soins.php');
    exit;
}

// Récupération des tags associés
$stmt = $pdo->prepare("
    SELECT tag.nom
    FROM tag
    JOIN item_tag ON tag.id = item_tag.tag_id
    WHERE item_tag.item_id = :id
    ORDER BY tag.nom
");
$stmt->execute(['id' => $soin['id']]);
$tags = array_column($stmt->fetchAll(), 'nom');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($soin['titre']) ?> – BE CHILL</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <p>BE CHILL</p>
        <p>Salon de massage et bien-être à Bruxelles</p>
    </header>

    <nav aria-label="Navigation principale">
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="soins.php" aria-current="page">Nos soins</a></li>
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
        <nav aria-label="Fil d'Ariane">
            <ol>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="soins.php">Nos soins</a></li>
                <li aria-current="page"><?= htmlspecialchars($soin['titre']) ?></li>
            </ol>
        </nav>

        <article>
            <h1><?= htmlspecialchars($soin['titre']) ?></h1>

            <section aria-labelledby="description-soin">
                <h2 id="description-soin">Description</h2>
                <p><?= nl2br(htmlspecialchars($soin['description'])) ?></p>
            </section>

            <section aria-labelledby="details-pratiques">
                <h2 id="details-pratiques">Détails pratiques</h2>
                <dl>
                    <dt>Durée</dt>
                    <dd><?= (int)$soin['duree'] ?> minutes</dd>

                    <dt>Prix</dt>
                    <dd><?= number_format((float)$soin['prix'], 2, ',', ' ') ?> €</dd>

                    <dt>Catégorie</dt>
                    <dd><?= htmlspecialchars($soin['categorie']) ?></dd>

                    <dt>Thème</dt>
                    <dd><?= htmlspecialchars($soin['theme']) ?></dd>

                    <?php if (!empty($tags)): ?>
                        <dt>Mots-clés</dt>
                        <dd><?= htmlspecialchars(implode(', ', $tags)) ?></dd>
                    <?php endif; ?>
                </dl>
            </section>

            <p><a href="reservation.html">Réserver <?= htmlspecialchars(mb_strtolower($soin['titre'])) ?></a></p>
            <p><a href="soins.php">Retour à la liste des soins</a></p>
        </article>
    </main>

    <footer>
        <section aria-labelledby="coordonnees-soin">
            <h2 id="coordonnees-soin">Coordonnées</h2>
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
