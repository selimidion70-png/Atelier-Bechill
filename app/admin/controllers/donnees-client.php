<?php
// RGPD : retrouver et supprimer les données d'un client à partir de son email
// (droit d'accès / droit à l'effacement). Page réservée à l'administrateur.
require_once __DIR__ . '/../../models/rgpd.php';

$email     = trim($_GET['email'] ?? '');
$supprime  = isset($_GET['supprime']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
    rgpd_supprimer_client($pdo, $_POST['email']);
    redirect('/admin/donnees-client?supprime=1');
}

echo render($base . '/views/donnees-client.php', [
    'email'     => $email,
    'supprime'  => $supprime,
    'donnees'   => filter_var($email, FILTER_VALIDATE_EMAIL) ? rgpd_donnees_client($pdo, $email) : null,
    'pageTitle' => 'Données d\'un client – Admin',
]);
