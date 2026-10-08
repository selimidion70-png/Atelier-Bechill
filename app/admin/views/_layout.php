<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Administration BE CHILL') ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<header class="site-header">
    <a href="/" class="site-header__brand">BE CHILL <span class="site-header__admin-badge">Admin</span></a>
    <?php if (!empty($_SESSION['operator_nom'])): ?>
        <p class="site-header__tagline">Connecté en tant que : <?= htmlspecialchars($_SESSION['operator_nom']) ?></p>
    <?php endif; ?>
</header>

<?php if (!empty($_SESSION['operator_id'])): ?>
<nav aria-label="Navigation administration">
    <ul>
        <li><a href="/admin">Tableau de bord</a></li>
        <li><a href="/admin/soins">Gérer les soins</a></li>
        <li><a href="/admin/reservations">Réservations</a></li>
        <li><a href="/admin/messages">Messages reçus</a></li>
        <li><a href="/admin/logout">Déconnexion</a></li>
    </ul>
</nav>
<?php endif; ?>

<?= $page_content ?>

<footer>
    <p><small>&copy; 2026 BE CHILL – Espace d'administration</small></p>
</footer>

</body>
</html>
