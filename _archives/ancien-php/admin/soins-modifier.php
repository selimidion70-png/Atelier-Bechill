<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: soins.php');
    exit;
}

// Récupération du soin existant
$stmt = $pdo->prepare("SELECT * FROM item WHERE id = :id");
$stmt->execute(['id' => $id]);
$soin = $stmt->fetch();

if (!$soin) {
    header('Location: soins.php');
    exit;
}

// Listes pour les select
$categories = $pdo->query("SELECT id, nom FROM category ORDER BY nom")->fetchAll();
$themes = $pdo->query("SELECT id, nom FROM theme ORDER BY nom")->fetchAll();
$tagsDisponibles = $pdo->query("SELECT id, nom FROM tag ORDER BY nom")->fetchAll();

// Tags actuellement associés au soin
$stmt = $pdo->prepare("SELECT tag_id FROM item_tag WHERE item_id = :id");
$stmt->execute(['id' => $id]);
$tagsActuels = array_column($stmt->fetchAll(), 'tag_id');

$erreurs = [];

// Valeurs par défaut = valeurs actuelles en BDD
$valeurs = [
    'titre' => $soin['titre'],
    'category_id' => $soin['category_id'],
    'theme_id' => $soin['theme_id'],
    'description_courte' => $soin['description_courte'],
    'description' => $soin['description'],
    'duree' => $soin['duree'],
    'prix' => $soin['prix'],
    'statut' => $soin['statut'],
    'tags' => $tagsActuels,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['titre'] = trim($_POST['nom-soin'] ?? '');
    $valeurs['category_id'] = $_POST['categorie'] ?? '';
    $valeurs['theme_id'] = $_POST['theme'] ?? '';
    $valeurs['description_courte'] = trim($_POST['description-courte'] ?? '');
    $valeurs['description'] = trim($_POST['description-complete'] ?? '');
    $valeurs['duree'] = $_POST['duree'] ?? '';
    $valeurs['prix'] = $_POST['prix'] ?? '';
    $valeurs['statut'] = $_POST['statut'] ?? 'brouillon';
    $valeurs['tags'] = $_POST['tags'] ?? [];

    // --- Validation ---
    if ($valeurs['titre'] === '') {
        $erreurs[] = "Le nom du soin est obligatoire.";
    }
    if ($valeurs['category_id'] === '') {
        $erreurs[] = "La catégorie est obligatoire.";
    }
    if ($valeurs['theme_id'] === '') {
        $erreurs[] = "Le thème est obligatoire.";
    }
    if ($valeurs['description_courte'] === '') {
        $erreurs[] = "La description courte est obligatoire.";
    }
    if ($valeurs['description'] === '') {
        $erreurs[] = "La description complète est obligatoire.";
    }
    if ($valeurs['duree'] === '' || !is_numeric($valeurs['duree']) || (int)$valeurs['duree'] <= 0) {
        $erreurs[] = "La durée doit être un nombre positif.";
    }
    if ($valeurs['prix'] === '' || !is_numeric($valeurs['prix']) || (float)$valeurs['prix'] < 0) {
        $erreurs[] = "Le prix doit être un nombre positif.";
    }

    // Vérification unicité du titre (sauf pour ce même item)
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id FROM item WHERE titre = :titre AND id != :id");
        $stmt->execute(['titre' => $valeurs['titre'], 'id' => $id]);
        if ($stmt->fetch()) {
            $erreurs[] = "Un autre soin porte déjà ce nom.";
        }
    }

    // --- Mise à jour ---
    if (empty($erreurs)) {
        // Régénération du slug si le titre a changé
        $slug = strtolower($valeurs['titre']);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE item SET
                titre = :titre,
                slug = :slug,
                description_courte = :description_courte,
                description = :description,
                duree = :duree,
                prix = :prix,
                statut = :statut,
                theme_id = :theme_id,
                category_id = :category_id
            WHERE id = :id
        ");
        $stmt->execute([
            'titre' => $valeurs['titre'],
            'slug' => $slug,
            'description_courte' => $valeurs['description_courte'],
            'description' => $valeurs['description'],
            'duree' => (int)$valeurs['duree'],
            'prix' => (float)$valeurs['prix'],
            'statut' => $valeurs['statut'],
            'theme_id' => (int)$valeurs['theme_id'],
            'category_id' => (int)$valeurs['category_id'],
            'id' => $id,
        ]);

        // Mise à jour des tags : on supprime tout puis on réinsère
        $stmt = $pdo->prepare("DELETE FROM item_tag WHERE item_id = :id");
        $stmt->execute(['id' => $id]);

        if (!empty($valeurs['tags'])) {
            $stmtTag = $pdo->prepare("INSERT INTO item_tag (item_id, tag_id) VALUES (:item_id, :tag_id)");
            foreach ($valeurs['tags'] as $tagId) {
                $stmtTag->execute([
                    'item_id' => $id,
                    'tag_id' => (int)$tagId,
                ]);
            }
        }

        $pdo->commit();

        header('Location: soins.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un soin – Administration BE CHILL</title>
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
        <h1>Modifier un soin : <?= htmlspecialchars($soin['titre']) ?></h1>
        <p><a href="soins.php">Retour à la liste des soins</a></p>

        <?php if (!empty($erreurs)): ?>
            <div role="alert" style="color: red;">
                <p>Veuillez corriger les erreurs suivantes :</p>
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?= htmlspecialchars($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="soins-modifier.php?id=<?= $id ?>" method="post">
            <fieldset>
                <legend>Informations générales</legend>

                <p>
                    <label for="nom-soin">Nom du soin</label><br>
                    <input type="text" id="nom-soin" name="nom-soin" required
                           value="<?= htmlspecialchars($valeurs['titre']) ?>">
                </p>

                <p>
                    <label for="categorie">Catégorie</label><br>
                    <select id="categorie" name="categorie" required>
                        <option value="">-- Choisissez une catégorie --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ((string)$valeurs['category_id'] === (string)$cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="theme">Thème</label><br>
                    <select id="theme" name="theme" required>
                        <option value="">-- Choisissez un thème --</option>
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?= $theme['id'] ?>" <?= ((string)$valeurs['theme_id'] === (string)$theme['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($theme['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="description-courte">Description courte</label><br>
                    <input type="text" id="description-courte" name="description-courte" required
                           value="<?= htmlspecialchars($valeurs['description_courte']) ?>">
                </p>

                <p>
                    <label for="description-complete">Description complète</label><br>
                    <textarea id="description-complete" name="description-complete" rows="6" cols="60" required><?= htmlspecialchars($valeurs['description']) ?></textarea>
                </p>
            </fieldset>

            <fieldset>
                <legend>Tarification et durée</legend>

                <p>
                    <label for="duree">Durée (en minutes)</label><br>
                    <input type="number" id="duree" name="duree" min="15" max="180" step="15" required
                           value="<?= htmlspecialchars((string)$valeurs['duree']) ?>">
                </p>

                <p>
                    <label for="prix">Prix (en euros)</label><br>
                    <input type="number" id="prix" name="prix" min="0" step="0.01" required
                           value="<?= htmlspecialchars((string)$valeurs['prix']) ?>">
                </p>
            </fieldset>

            <fieldset>
                <legend>Tags</legend>
                <p>Sélectionnez les mots-clés correspondant à ce soin :</p>
                <?php foreach ($tagsDisponibles as $tag): ?>
                    <p>
                        <input type="checkbox" id="tag-<?= $tag['id'] ?>" name="tags[]" value="<?= $tag['id'] ?>"
                               <?= in_array($tag['id'], $valeurs['tags']) ? 'checked' : '' ?>>
                        <label for="tag-<?= $tag['id'] ?>"><?= htmlspecialchars($tag['nom']) ?></label>
                    </p>
                <?php endforeach; ?>
            </fieldset>

            <fieldset>
                <legend>Publication</legend>

                <p>
                    <label for="statut">Statut</label><br>
                    <select id="statut" name="statut" required>
                        <option value="publie" <?= $valeurs['statut'] === 'publie' ? 'selected' : '' ?>>Actif (visible sur le site)</option>
                        <option value="brouillon" <?= $valeurs['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon (non visible)</option>
                        <option value="archive" <?= $valeurs['statut'] === 'archive' ? 'selected' : '' ?>>Inactif (archivé)</option>
                    </select>
                </p>
            </fieldset>

            <p>
                <button type="submit">Enregistrer les modifications</button>
                <a href="soins.php">Annuler et revenir à la liste</a>
            </p>
        </form>
    </main>

    <footer>
        <p><small>&copy; 2026 BE CHILL – Espace d'administration</small></p>
    </footer>

</body>
</html>
