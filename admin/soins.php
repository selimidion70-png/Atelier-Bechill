<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// --- Traitement des actions rapides (changement de statut) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int) $_POST['id'];

    if ($_POST['action'] === 'desactiver') {
        $stmt = $pdo->prepare("UPDATE item SET statut = 'archive' WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } elseif ($_POST['action'] === 'activer') {
        $stmt = $pdo->prepare("UPDATE item SET statut = 'publie' WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } elseif ($_POST['action'] === 'supprimer') {
        $stmt = $pdo->prepare("DELETE FROM item WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    header('Location: soins.php' . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// --- Récupération des catégories pour le filtre ---
$categories = $pdo->query("SELECT id, nom FROM category ORDER BY nom")->fetchAll();

// --- Lecture des filtres ---
$recherche = trim($_GET['recherche'] ?? '');
$categorieFiltre = $_GET['categorie'] ?? '';
$statutFiltre = $_GET['statut'] ?? '';

// --- Construction de la requête dynamique ---
$sql = "
    SELECT item.id, item.titre, item.duree, item.prix, item.statut, category.nom AS categorie, category.id AS categorie_id
    FROM item
    JOIN category ON item.category_id = category.id
    WHERE 1=1
";
$params = [];

if ($recherche !== '') {
    $sql .= " AND item.titre LIKE :recherche";
    $params['recherche'] = '%' . $recherche . '%';
}

if ($categorieFiltre !== '') {
    $sql .= " AND category.id = :categorie_id";
    $params['categorie_id'] = $categorieFiltre;
}

if ($statutFiltre !== '') {
    $sql .= " AND item.statut = :statut";
    $params['statut'] = $statutFiltre;
}

$sql .= " ORDER BY item.titre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$soins = $stmt->fetchAll();

// Libellés lisibles pour le statut
$libellesStatut = [
    'publie' => 'Actif',
    'brouillon' => 'Brouillon',
    'archive' => 'Inactif',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les soins – Administration BE CHILL</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header>
        <p>BE CHILL – Administration</p>
        <p>Connecté en tant que : <?= htmlspecialchars($_SESSION['operator_nom']) ?></p>
    </header>

    <nav aria-label="Navigation administration">
        <ul>
            <li><a href="dashboard.php">Tableau de bord</a></li>
            <li><a href="soins.php" aria-current="page">Gérer les soins</a></li>
            <li><a href="messages.php">Messages reçus</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main>
        <h1>Gestion des soins</h1>
        <p>Consultez, modifiez ou supprimez les soins proposés sur le site.</p>

        <section aria-labelledby="filtres-soins">
            <h2 id="filtres-soins">Filtrer les soins</h2>
            <form action="soins.php" method="get">
                <fieldset>
                    <legend>Critères de recherche</legend>

                    <p>
                        <label for="recherche-soin">Rechercher par nom</label><br>
                        <input type="search" id="recherche-soin" name="recherche" placeholder="Ex : massage relaxant"
                               value="<?= htmlspecialchars($recherche) ?>">
                    </p>

                    <p>
                        <label for="categorie-filtre">Catégorie</label><br>
                        <select id="categorie-filtre" name="categorie">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ((string)$categorieFiltre === (string)$cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="statut-filtre">Statut</label><br>
                        <select id="statut-filtre" name="statut">
                            <option value="">Tous les statuts</option>
                            <option value="publie" <?= $statutFiltre === 'publie' ? 'selected' : '' ?>>Actif</option>
                            <option value="archive" <?= $statutFiltre === 'archive' ? 'selected' : '' ?>>Inactif</option>
                            <option value="brouillon" <?= $statutFiltre === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        </select>
                    </p>

                    <p>
                        <button type="submit">Filtrer</button>
                        <a href="soins.php">Réinitialiser les filtres</a>
                    </p>
                </fieldset>
            </form>
        </section>

        <section aria-labelledby="liste-soins">
            <h2 id="liste-soins">Liste des soins</h2>

            <p><a href="soins-ajouter.php">Ajouter un nouveau soin</a></p>

            <table>
                <caption>Liste complète des soins proposés</caption>
                <thead>
                    <tr>
                        <th scope="col">Nom du soin</th>
                        <th scope="col">Catégorie</th>
                        <th scope="col">Durée</th>
                        <th scope="col">Prix</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($soins)): ?>
                        <tr>
                            <td colspan="6">Aucun soin ne correspond à votre recherche.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($soins as $soin): ?>
                            <tr>
                                <td><?= htmlspecialchars($soin['titre']) ?></td>
                                <td><?= htmlspecialchars($soin['categorie']) ?></td>
                                <td><?= (int)$soin['duree'] ?> min</td>
                                <td><?= number_format((float)$soin['prix'], 2, ',', ' ') ?> €</td>
                                <td><?= htmlspecialchars($libellesStatut[$soin['statut']] ?? $soin['statut']) ?></td>
                                <td>
                                    <a href="soins-modifier.php?id=<?= $soin['id'] ?>">Modifier ce soin</a>
                                    |
                                    <?php if ($soin['statut'] === 'publie'): ?>
                                        <form action="soins.php<?= $_SERVER['QUERY_STRING'] !== '' ? '?' . htmlspecialchars($_SERVER['QUERY_STRING']) : '' ?>" method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $soin['id'] ?>">
                                            <input type="hidden" name="action" value="desactiver">
                                            <button type="submit">Désactiver ce soin</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="soins.php<?= $_SERVER['QUERY_STRING'] !== '' ? '?' . htmlspecialchars($_SERVER['QUERY_STRING']) : '' ?>" method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $soin['id'] ?>">
                                            <input type="hidden" name="action" value="activer">
                                            <button type="submit">Activer ce soin</button>
                                        </form>
                                    <?php endif; ?>
                                    |
                                    <form action="soins.php<?= $_SERVER['QUERY_STRING'] !== '' ? '?' . htmlspecialchars($_SERVER['QUERY_STRING']) : '' ?>" method="post" style="display:inline;"
                                          onsubmit="return confirm('Supprimer définitivement ce soin ?');">
                                        <input type="hidden" name="id" value="<?= $soin['id'] ?>">
                                        <input type="hidden" name="action" value="supprimer">
                                        <button type="submit">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p><small>&copy; 2026 BE CHILL – Espace d'administration</small></p>
    </footer>

</body>
</html>
