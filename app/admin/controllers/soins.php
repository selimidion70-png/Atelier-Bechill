<?php
require_once __DIR__ . '/../../models/item.php';

// Supprimer un soin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    item_delete($pdo, (int)$_POST['supprimer_id']);
    redirect('/admin/soins');
}

// Changer le statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut_id'], $_POST['statut'])
    && in_array($_POST['statut'], ['brouillon', 'publie', 'archive'], true)) {
    item_set_statut($pdo, (int)$_POST['statut_id'], $_POST['statut']);
    redirect('/admin/soins');
}

$soins = item_get_all($pdo);

echo render($base . '/views/soins.php', [
    'soins'     => $soins,
    'pageTitle' => 'Gestion des soins – Admin',
]);
