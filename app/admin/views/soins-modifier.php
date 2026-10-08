<main>
    <nav aria-label="Fil d'Ariane">
        <ol>
            <li><a href="/admin">Tableau de bord</a></li>
            <li><a href="/admin/soins">Soins</a></li>
            <li aria-current="page">Modifier un soin</li>
        </ol>
    </nav>

    <h1>Modifier un soin : <?= htmlspecialchars($titreActuel) ?></h1>

    <?php if (!empty($erreurs)): ?>
        <div role="alert">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/admin/soins-modifier?id=<?= $id ?>" method="post">
        <?= csrf_field() ?>
        <?php require __DIR__ . '/_soin-champs.php'; ?>

        <p>
            <button type="submit">Enregistrer les modifications</button>
            <a href="/admin/soins">Annuler</a>
        </p>
    </form>
</main>
