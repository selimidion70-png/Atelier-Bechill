<?php
require_once __DIR__ . '/db.php';

// --- Listes pour les filtres ---
$categories = $pdo->query("SELECT id, nom FROM category ORDER BY nom")->fetchAll();
$themes = $pdo->query("SELECT id, nom FROM theme ORDER BY nom")->fetchAll();

// --- Lecture des filtres ---
$recherche = trim($_GET['recherche'] ?? '');
$categorieFiltre = $_GET['categorie'] ?? '';
$themeFiltre = $_GET['theme'] ?? '';
$dureeFiltre = $_GET['duree'] ?? '';

// --- Construction de la requête ---
$sql = "
    SELECT item.id, item.titre, item.slug, item.description_courte, item.duree, item.prix,
           category.id AS category_id, category.nom AS categorie,
           theme.id AS theme_id, theme.nom AS theme
    FROM item
    JOIN category ON item.category_id = category.id
    JOIN theme ON item.theme_id = theme.id
    WHERE item.statut = 'publie'
";
$params = [];

if ($recherche !== '') {
    // Recherche dans le titre, la description courte, la description complète et les tags
    $sql .= " AND (
        item.titre LIKE :recherche1
        OR item.description_courte LIKE :recherche2
        OR item.description LIKE :recherche3
        OR item.id IN (
            SELECT item_tag.item_id FROM item_tag
            JOIN tag ON item_tag.tag_id = tag.id
            WHERE tag.nom LIKE :recherche4
        )
    )";
    $like = '%' . $recherche . '%';
    $params['recherche1'] = $like;
    $params['recherche2'] = $like;
    $params['recherche3'] = $like;
    $params['recherche4'] = $like;
}

if ($categorieFiltre !== '') {
    $sql .= " AND category.id = :categorie_id";
    $params['categorie_id'] = $categorieFiltre;
}

if ($themeFiltre !== '') {
    $sql .= " AND theme.id = :theme_id";
    $params['theme_id'] = $themeFiltre;
}

if ($dureeFiltre !== '' && is_numeric($dureeFiltre)) {
    $sql .= " AND item.duree <= :duree";
    $params['duree'] = (int)$dureeFiltre;
}

$sql .= " ORDER BY category.nom, item.titre";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$soins = $stmt->fetchAll();

// Regroupement par catégorie pour l'affichage
$soinsParCategorie = [];
foreach ($soins as $soin) {
    $soinsParCategorie[$soin['categorie']][] = $soin;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos soins – BE CHILL</title>
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
            <input type="search" id="recherche-nav" name="recherche" placeholder="Ex : relaxant, sportif…"
                   value="<?= htmlspecialchars($recherche) ?>">
            <button type="submit">Rechercher</button>
        </form>
    </nav>

    <main>
        <h1>Nos soins</h1>
        <p>Découvrez notre gamme de massages et soins bien-être. Chaque soin est réalisé par un thérapeute certifié, dans un cadre calme et apaisant.</p>

        <section aria-labelledby="filtres-catalogue">
            <h2 id="filtres-catalogue">Rechercher un soin</h2>
            <form action="soins.php" method="get">
                <fieldset>
                    <legend>Filtrer le catalogue</legend>

                    <p>
                        <label for="recherche-soin">Rechercher par mot-clé</label><br>
                        <input type="search" id="recherche-soin" name="recherche" placeholder="Ex : relaxant, sportif, pierres"
                               value="<?= htmlspecialchars($recherche) ?>">
                    </p>

                    <p>
                        <label for="filtre-categorie">Catégorie</label><br>
                        <select id="filtre-categorie" name="categorie">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ((string)$categorieFiltre === (string)$cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="filtre-theme">Thème</label><br>
                        <select id="filtre-theme" name="theme">
                            <option value="">Tous les thèmes</option>
                            <?php foreach ($themes as $th): ?>
                                <option value="<?= $th['id'] ?>" <?= ((string)$themeFiltre === (string)$th['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($th['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="filtre-duree">Durée maximale</label><br>
                        <select id="filtre-duree" name="duree">
                            <option value="">Toutes les durées</option>
                            <option value="30" <?= $dureeFiltre === '30' ? 'selected' : '' ?>>30 minutes</option>
                            <option value="45" <?= $dureeFiltre === '45' ? 'selected' : '' ?>>45 minutes</option>
                            <option value="60" <?= $dureeFiltre === '60' ? 'selected' : '' ?>>60 minutes</option>
                            <option value="90" <?= $dureeFiltre === '90' ? 'selected' : '' ?>>90 minutes</option>
                        </select>
                    </p>

                    <p>
                        <button type="submit">Rechercher</button>
                        <a href="soins.php">Réinitialiser les filtres</a>
                    </p>
                </fieldset>
            </form>
        </section>

        <?php if (empty($soins)): ?>
            <section aria-labelledby="aucun-resultat">
                <h2 id="aucun-resultat">Aucun résultat</h2>
                <p>Aucun soin ne correspond à votre recherche. Essayez d'autres critères.</p>
            </section>
        <?php else: ?>
            <?php foreach ($soinsParCategorie as $nomCategorie => $soinsCategorie): ?>
                <section aria-labelledby="categorie-<?= md5($nomCategorie) ?>">
                    <h2 id="categorie-<?= md5($nomCategorie) ?>"><?= htmlspecialchars($nomCategorie) ?></h2>

                    <?php foreach ($soinsCategorie as $soin): ?>
                        <article>
                            <h3><?= htmlspecialchars($soin['titre']) ?></h3>
                            <p><strong>Durée :</strong> <?= (int)$soin['duree'] ?> minutes</p>
                            <p><strong>Prix :</strong> <?= number_format((float)$soin['prix'], 2, ',', ' ') ?> €</p>
                            <p><strong>Thème :</strong> <?= htmlspecialchars($soin['theme']) ?></p>
                            <p><?= htmlspecialchars($soin['description_courte']) ?></p>
                            <p><a href="soin-detail.php?slug=<?= urlencode($soin['slug']) ?>">Voir le détail de <?= htmlspecialchars(mb_strtolower($soin['titre'])) ?></a></p>
                            <p><a href="reservation.html">Réserver <?= htmlspecialchars(mb_strtolower($soin['titre'])) ?></a></p>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>

        <aside aria-labelledby="bon-a-savoir">
            <h2 id="bon-a-savoir">Bon à savoir</h2>
            <ul>
                <li>Arrivez 10 minutes avant votre rendez-vous pour vous installer.</li>
                <li>Signalez toute allergie ou condition médicale à votre thérapeute.</li>
                <li>Des serviettes et peignoirs sont fournis sur place.</li>
                <li>Annulation gratuite jusqu'à 24h avant le rendez-vous.</li>
            </ul>
        </aside>
    </main>

    <footer>
        <section aria-labelledby="coordonnees-soins">
            <h2 id="coordonnees-soins">Coordonnées</h2>
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
