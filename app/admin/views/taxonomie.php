<main>
    <h1><?= $tx['titre'] ?></h1>

    <?php if ($erreur !== ''): ?>
        <div role="alert"><p><?= htmlspecialchars($erreur) ?></p></div>
    <?php endif; ?>

    <form action="<?= $url ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="ajouter">
        <fieldset>
            <legend>Ajouter un(e) <?= $tx['singulier'] ?></legend>
            <p>
                <label for="nouveau-nom">Nom</label>
                <input type="text" id="nouveau-nom" name="nom" required maxlength="100">
                <button type="submit">Ajouter</button>
            </p>
        </fieldset>
    </form>

    <?php if (empty($elements)): ?>
        <p role="status">Aucun élément pour le moment.</p>
    <?php else: ?>
        <table>
            <caption>Liste des <?= mb_strtolower($tx['titre']) ?></caption>
            <thead>
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Soins concernés</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($elements as $el): ?>
                    <tr>
                        <td>
                            <form action="<?= $url ?>" method="post" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="renommer">
                                <input type="hidden" name="id" value="<?= $el['id'] ?>">
                                <label for="nom-<?= $el['id'] ?>" class="visually-hidden">Nom</label>
                                <input type="text" id="nom-<?= $el['id'] ?>" name="nom" required maxlength="100"
                                       value="<?= htmlspecialchars($el['nom']) ?>">
                                <button type="submit">Renommer</button>
                            </form>
                        </td>
                        <td><?= (int)$el['nb_soins'] ?></td>
                        <td>
                            <form action="<?= $url ?>" method="post" style="display:inline"
                                  onsubmit="return confirm('Supprimer « <?= htmlspecialchars(addslashes($el['nom'])) ?> » ?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= $el['id'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
