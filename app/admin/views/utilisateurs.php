<main>
    <h1>Utilisateurs</h1>
    <p>
        <strong>Administrateur</strong> : accès à tout.
        <strong>Éditeur</strong> : gère le contenu du site (soins, catégories, thèmes, tags, collections),
        sans accès aux réservations, aux messages ni aux comptes.
    </p>

    <?php if (!empty($erreurs)): ?>
        <div role="alert">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <table>
        <caption>Comptes de l'espace d'administration</caption>
        <thead>
            <tr>
                <th scope="col">Nom</th>
                <th scope="col">Courriel</th>
                <th scope="col">Rôle</th>
                <th scope="col">Statut</th>
                <th scope="col">Nouveau mot de passe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?><?= (int)$u['id'] === $moi ? ' <small>(vous)</small>' : '' ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php if ((int)$u['id'] === $moi): ?>
                            <?= ADMIN_ROLES[$u['role']] ?>
                        <?php else: ?>
                            <form action="/admin/utilisateurs" method="post" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="role">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <label for="role-<?= $u['id'] ?>" class="visually-hidden">Rôle de <?= htmlspecialchars($u['nom']) ?></label>
                                <select id="role-<?= $u['id'] ?>" name="role">
                                    <?php foreach (ADMIN_ROLES as $valeur => $libelle): ?>
                                        <option value="<?= $valeur ?>" <?= $u['role'] === $valeur ? 'selected' : '' ?>><?= $libelle ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">Changer</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $u['actif'] ? 'Actif' : 'Désactivé' ?>
                        <?php if ((int)$u['id'] !== $moi): ?>
                            <form action="/admin/utilisateurs" method="post" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="actif">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="actif" value="<?= $u['actif'] ? '0' : '1' ?>">
                                <button type="submit"><?= $u['actif'] ? 'Désactiver' : 'Réactiver' ?></button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="/admin/utilisateurs" method="post" style="display:inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="mot_de_passe">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <label for="mdp-<?= $u['id'] ?>" class="visually-hidden">Nouveau mot de passe pour <?= htmlspecialchars($u['nom']) ?></label>
                            <input type="password" id="mdp-<?= $u['id'] ?>" name="mot_de_passe" minlength="<?= OPERATOR_MDP_MIN ?>" required autocomplete="new-password">
                            <button type="submit">Définir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="/admin/utilisateurs" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="ajouter">
        <fieldset>
            <legend>Créer un compte</legend>
            <p>
                <label for="nouveau-nom">Nom</label>
                <input type="text" id="nouveau-nom" name="nom" required maxlength="100"
                       value="<?= htmlspecialchars($nouveau['nom']) ?>">
            </p>
            <p>
                <label for="nouveau-email">Courriel (identifiant de connexion)</label>
                <input type="email" id="nouveau-email" name="email" required maxlength="150" autocomplete="off"
                       value="<?= htmlspecialchars($nouveau['email']) ?>">
            </p>
            <p>
                <label for="nouveau-mdp">Mot de passe (<?= OPERATOR_MDP_MIN ?> caractères minimum)</label>
                <input type="password" id="nouveau-mdp" name="mot_de_passe" required minlength="<?= OPERATOR_MDP_MIN ?>" autocomplete="new-password">
            </p>
            <p>
                <label for="nouveau-role">Rôle</label>
                <select id="nouveau-role" name="role">
                    <?php foreach (ADMIN_ROLES as $valeur => $libelle): ?>
                        <option value="<?= $valeur ?>" <?= $nouveau['role'] === $valeur ? 'selected' : '' ?>><?= $libelle ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><button type="submit">Créer le compte</button></p>
        </fieldset>
    </form>
</main>
