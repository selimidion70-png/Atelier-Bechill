<main>
    <h1>Collections</h1>
    <p>Les collections regroupent plusieurs soins (ex. « Spécial sportifs », « Idées cadeaux »). Elles sont affichées sur la page d'accueil.</p>
    <p><a href="/admin/collections-modifier">+ Nouvelle collection</a></p>

    <?php if (empty($collections)): ?>
        <p role="status">Aucune collection pour le moment.</p>
    <?php else: ?>
        <table>
            <caption>Liste des collections</caption>
            <thead>
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Soins</th>
                    <th scope="col">Créée par</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($collections as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nom']) ?></td>
                        <td><?= (int)$c['nb_soins'] ?></td>
                        <td><?= htmlspecialchars($c['auteur']) ?>, le <?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                        <td>
                            <a href="/admin/collections-modifier?id=<?= $c['id'] ?>">Modifier</a>
                            <form action="/admin/collections" method="post" style="display:inline"
                                  onsubmit="return confirm('Supprimer cette collection ? (les soins ne sont pas supprimés)')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="supprimer_id" value="<?= $c['id'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
