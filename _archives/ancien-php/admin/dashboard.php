<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

// Indicateurs dynamiques
$nbSoinsActifs = $pdo->query("SELECT COUNT(*) FROM item WHERE statut = 'publie'")->fetchColumn();
$nbMessagesNonLus = $pdo->query("SELECT COUNT(*) FROM message WHERE lu = 0")->fetchColumn();
$nbSoinsTotal = $pdo->query("SELECT COUNT(*) FROM item")->fetchColumn();

// Derniers soins ajoutés (à la place des "dernières réservations", hors périmètre BDD)
$stmt = $pdo->query("
    SELECT item.titre, item.date_creation, category.nom AS categorie, item.statut
    FROM item
    JOIN category ON item.category_id = category.id
    ORDER BY item.date_creation DESC
    LIMIT 5
");
$derniersSoins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord – Administration BE CHILL</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header>
        <p>BE CHILL – Administration</p>
        <p>Connecté en tant que : <?= htmlspecialchars($_SESSION['operator_nom']) ?></p>
    </header>

    <nav aria-label="Navigation administration">
        <ul>
            <li><a href="dashboard.php" aria-current="page">Tableau de bord</a></li>
            <li><a href="soins.php">Gérer les soins</a></li>
            <li><a href="messages.php">Messages reçus</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main>
        <h1>Tableau de bord</h1>
        <p>Bienvenue dans l'espace d'administration de BE CHILL. Consultez les indicateurs ci-dessous et accédez aux différentes sections de gestion.</p>

        <section aria-labelledby="resume">
            <h2 id="resume">Résumé</h2>
            <table>
                <caption>Indicateurs du tableau de bord</caption>
                <thead>
                    <tr>
                        <th scope="col">Indicateur</th>
                        <th scope="col">Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Soins publiés</td>
                        <td><?= $nbSoinsActifs ?></td>
                    </tr>
                    <tr>
                        <td>Soins au total</td>
                        <td><?= $nbSoinsTotal ?></td>
                    </tr>
                    <tr>
                        <td>Messages non lus</td>
                        <td><?= $nbMessagesNonLus ?></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section aria-labelledby="acces-rapide">
            <h2 id="acces-rapide">Accès rapide</h2>
            <ul>
                <li><a href="soins-ajouter.php">Ajouter un nouveau soin</a></li>
                <li><a href="messages.php">Lire les messages</a></li>
                <li><a href="../index.php">Voir le site public</a></li>
            </ul>
        </section>

        <section aria-labelledby="derniers-soins">
            <h2 id="derniers-soins">Derniers soins ajoutés</h2>
            <table>
                <caption>Les 5 derniers soins créés ou modifiés</caption>
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Nom du soin</th>
                        <th scope="col">Catégorie</th>
                        <th scope="col">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($derniersSoins)): ?>
                        <tr>
                            <td colspan="4">Aucun soin enregistré pour le moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($derniersSoins as $soin): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($soin['date_creation'])) ?></td>
                                <td><?= htmlspecialchars($soin['titre']) ?></td>
                                <td><?= htmlspecialchars($soin['categorie']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($soin['statut'])) ?></td>
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
