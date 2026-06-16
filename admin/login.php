<?php
session_start();
require_once 'C:/laragon/www/Atelier-Bechill/admin/db.php';

// Si déjà connecté, redirection directe vers le tableau de bord
if (isset($_SESSION['operator_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['identifiant'] ?? '');
    $motDePasse = $_POST['mot-de-passe'] ?? '';

    if ($email === '' || $motDePasse === '') {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nom, email, mot_de_passe, role, actif FROM operator WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $operator = $stmt->fetch();

        if (!$operator) {
            $erreur = "Identifiant ou mot de passe incorrect.";
        } elseif (!$operator['actif']) {
            $erreur = "Ce compte est désactivé.";
        } elseif (!password_verify($motDePasse, $operator['mot_de_passe'])) {
            $erreur = "Identifiant ou mot de passe incorrect.";
        } elseif ($operator['role'] !== 'admin') {
            $erreur = "Vous n'avez pas accès à l'espace d'administration.";
        } else {
            // Connexion réussie
            $_SESSION['operator_id'] = $operator['id'];
            $_SESSION['operator_nom'] = $operator['nom'];
            $_SESSION['operator_role'] = $operator['role'];

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – Administration BE CHILL</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header>
        <p>BE CHILL – Administration</p>
    </header>

    <main>
        <h1>Connexion à l'espace d'administration</h1>
        <p>Veuillez saisir vos identifiants pour accéder au tableau de bord.</p>

        <?php if ($erreur !== ''): ?>
            <p role="alert" style="color: red;"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <fieldset>
                <legend>Identifiants de connexion</legend>

                <p>
                    <label for="identifiant">Identifiant (email)</label><br>
                    <input type="text" id="identifiant" name="identifiant" autocomplete="username" required
                           value="<?= htmlspecialchars($_POST['identifiant'] ?? '') ?>">
                </p>

                <p>
                    <label for="mot-de-passe">Mot de passe</label><br>
                    <input type="password" id="mot-de-passe" name="mot-de-passe" autocomplete="current-password" required>
                </p>

                <p>
                    <input type="checkbox" id="se-souvenir" name="se-souvenir" value="oui">
                    <label for="se-souvenir">Se souvenir de moi</label>
                </p>
            </fieldset>

            <p>
                <button type="submit">Se connecter</button>
            </p>
        </form>

        <p><a href="../index.html">Retour au site public</a></p>
    </main>

    <footer>
        <p><small>&copy; 2026 BE CHILL – Espace d'administration</small></p>
    </footer>

</body>
</html>
