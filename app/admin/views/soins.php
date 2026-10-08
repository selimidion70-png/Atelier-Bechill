<main>
    <h1>Gestion des soins</h1>
    <p><a href="/admin/soins-ajouter">+ Ajouter un nouveau soin</a></p>

    <?php if (empty($soins)): ?>
        <p role="status">Aucun soin enregistré pour le moment.</p>
    <?php else: ?>
        <table>
            <caption>Liste de tous les soins</caption>
            <thead>
                <tr>
                    <th scope="col">Photo</th>
                    <th scope="col">Titre</th>
                    <th scope="col">Catégorie</th>
                    <th scope="col">Durée</th>
                    <th scope="col">Prix</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soins as $soin): ?>
                    <tr>
                        <td><?php if ($soin['image']): ?><img src="<?= item_image_url($soin['image']) ?>" alt="" width="60"><?php else: ?>—<?php endif; ?></td>
                        <td><?= htmlspecialchars($soin['titre']) ?></td>
                        <td><?= htmlspecialchars($soin['categorie']) ?></td>
                        <td><?= (int)$soin['duree'] ?> min</td>
                        <td><?= number_format((float)$soin['prix'], 2, ',', ' ') ?> €</td>
                        <td><?= htmlspecialchars(ucfirst($soin['statut'])) ?></td>
                        <td>
                            <a href="/admin/soins-modifier?id=<?= $soin['id'] ?>">Modifier</a>

                            <form action="/admin/soins" method="post" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="statut_id" value="<?= $soin['id'] ?>">
                                <?php if ($soin['statut'] === 'publie'): ?>
                                    <input type="hidden" name="statut" value="brouillon">
                                    <button type="submit">Dépublier</button>
                                <?php else: ?>
                                    <input type="hidden" name="statut" value="publie">
                                    <button type="submit">Publier</button>
                                <?php endif; ?>
                            </form>

                            <form action="/admin/soins" method="post" style="display:inline"
                                  onsubmit="return confirm('Supprimer ce soin définitivement ?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="supprimer_id" value="<?= $soin['id'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
