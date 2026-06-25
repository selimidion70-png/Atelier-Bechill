<?php
require_once __DIR__ . '/../../models/message.php';

// Marquer lu / non-lu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lu_id'], $_POST['lu'])) {
    message_set_lu($pdo, (int)$_POST['lu_id'], (int)$_POST['lu']);
    redirect('/admin/messages');
}

// Supprimer un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    message_delete($pdo, (int)$_POST['supprimer_id']);
    redirect('/admin/messages');
}

$filtres = [
    'recherche' => trim($_GET['recherche'] ?? ''),
    'sujet'     => $_GET['sujet'] ?? '',
    'lu'        => $_GET['lu'] ?? '',
];

$messages = message_get_all($pdo, $filtres);

echo render($base . '/views/messages.php', [
    'messages'  => $messages,
    'filtres'   => $filtres,
    'pageTitle' => 'Messages reçus – Admin',
]);
