<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// --- Traitement des actions (marquer lu/non lu, supprimer) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int) $_POST['id'];

    if ($_POST['action'] === 'marquer-lu') {
        $stmt = $pdo->prepare("UPDATE message SET lu = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } elseif ($_POST['action'] === 'marquer-non-lu') {
        $stmt = $pdo->prepare("UPDATE message SET lu = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } elseif ($_POST['action'] === 'supprimer') {
        $stmt = $pdo->prepare("DELETE FROM message WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    header('Location: messages.php' . (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// --- Lecture des filtres ---
$recherche = trim($_GET['recherche'] ?? '');
$sujetFiltre = $_GET['sujet'] ?? '';
$luFiltre = $_GET['lu'] ?? '';

// --- Construction de la requête dynamique ---
$sql = "SELECT * FROM message WHERE 1=1";
$params = [];

if ($recherche !== '') {
    $sql .= " AND (nom LIKE :recherche OR sujet LIKE :recherche2)";
    $params['recherche'] = '%' . $recherche . '%';
    $params['recherche2'] = '%' . $recherche . '%';
}

if ($sujetFiltre !== '') {
    $sql .= " AND sujet = :sujet";
    $params['sujet'] = $sujetFiltre;
}

if ($luFiltre === 'lu') {
    $sql .= " AND lu = 1";
} elseif ($luFiltre === 'non-lu') {
    $sql .= " AND lu = 0";
}

$sql .= " ORDER BY date_envoi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Libellés lisibles pour les sujets
$libellesSujet = [
    'information' => "Demande d'information",
    'reservation' => "Question sur une réservation",
    'reclamation' => "Réclamation",
    'partenariat' => "Proposition de partenariat",
    'autre' => "Autre",
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages reçus – Administration BE CHILL</title>
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
            <li><a href="soins.php">Gérer les soins</a></li>
            <li><a href="messages.php" aria-current="page">Messages reçus</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main>
        <h1>Messages reçus</h1>
        <p>Consultez les messages envoyés via le formulaire de contact du site.</p>

        <section aria-labelledby="filtres-messages">
            <h2 id="filtres-messages">Filtrer les messages</h2>
            <form action="messages.php" method="get">
                <fieldset>
                    <legend>Critères de recherche</legend>

                    <p>
                        <label for="recherche-message">Rechercher par nom ou sujet</label><br>
                        <input type="search" id="recherche-message" name="recherche" placeholder="Ex : partenariat"
                               value="<?= htmlspecialchars($recherche) ?>">
                    </p>

                    <p>
                        <label for="filtre-sujet">Sujet</label><br>
                        <select id="filtre-sujet" name="sujet">
                            <option value="">Tous les sujets</option>
                            <?php foreach ($libellesSujet as $valeur => $libelle): ?>
                                <option value="<?= $valeur ?>" <?= $sujetFiltre === $valeur ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($libelle) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <label for="filtre-lu">État</label><br>
                        <select id="filtre-lu" name="lu">
                            <option value="">Tous</option>
                            <option value="non-lu" <?= $luFiltre === 'non-lu' ? 'selected' : '' ?>>Non lus</option>
                            <option value="lu" <?= $luFiltre === 'lu' ? 'selected' : '' ?>>Lus</option>
                        </select>
                    </p>

                    <p>
                        <button type="submit">Filtrer</button>
                        <a href="messages.php">Réinitialiser les filtres</a>
                    </p>
                </fieldset>
            </form>
        </section>

        <section aria-labelledby="liste-messages">
            <h2 id="liste-messages">Liste des messages</h2>
            <table>
                <caption>Messages reçus via le formulaire de contact</caption>
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Expéditeur</th>
                        <th scope="col">Courriel</th>
                        <th scope="col">Sujet</th>
                        <th scope="col">Message</th>
                        <th scope="col">État</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="7">Aucun message ne correspond à votre recherche.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($msg['date_envoi'])) ?></td>
                                <td><?= htmlspecialchars($msg['nom']) ?></td>
                                <td><?= htmlspecialchars($msg['email']) ?></td>
                                <td><?= htmlspecialchars($libellesSujet[$msg['sujet']] ?? $msg['sujet']) ?></td>
                                <td><?= htmlspecialchars(mb_strimwidth($msg['texte'], 0, 80, '…')) ?></td>
                                <td><?= $msg['lu'] ? 'Lu' : 'Non lu' ?></td>
                                <td>
                                    <?php $query = $_SERVER['QUERY_STRING'] !== '' ? '?' . htmlspecialchars($_SERVER['QUERY_STRING']) : ''; ?>
                                    <?php if ($msg['lu']): ?>
                                        <form action="messages.php<?= $query ?>" method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                            <input type="hidden" name="action" value="marquer-non-lu">
                                            <button type="submit">Marquer comme non lu</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="messages.php<?= $query ?>" method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                            <input type="hidden" name="action" value="marquer-lu">
                                            <button type="submit">Marquer comme lu</button>
                                        </form>
                                    <?php endif; ?>
                                    |
                                    <form action="messages.php<?= $query ?>" method="post" style="display:inline;"
                                          onsubmit="return confirm('Supprimer définitivement ce message ?');">
                                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
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
