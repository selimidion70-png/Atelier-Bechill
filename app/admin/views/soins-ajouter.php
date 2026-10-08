<main>
    <nav aria-label="Fil d'Ariane">
        <ol>
            <li><a href="/admin">Tableau de bord</a></li>
            <li><a href="/admin/soins">Soins</a></li>
            <li aria-current="page">Ajouter un soin</li>
        </ol>
    </nav>

    <h1>Ajouter un soin</h1>

    <?php if ($succes): ?>
        <p role="status">Le soin a bien été ajouté. <a href="/admin/soins">Retour à la liste</a></p>
    <?php endif; ?>

    <?php if (!empty($erreurs)): ?>
        <div role="alert">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/admin/soins-ajouter" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php require __DIR__ . '/_soin-champs.php'; ?>

        <p>
            <button type="submit">Enregistrer le soin</button>
            <a href="/admin/soins">Annuler</a>
        </p>
    </form>
</main>
