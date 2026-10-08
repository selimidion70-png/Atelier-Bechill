<?php
require_once __DIR__ . '/../../models/reservation.php';

// Changer le statut (confirmer / annuler / remettre en attente)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut_id'], $_POST['statut'])
    && array_key_exists($_POST['statut'], RESERVATION_STATUTS)) {
    reservation_set_statut($pdo, (int)$_POST['statut_id'], $_POST['statut']);
    redirect('/admin/reservations');
}

// Supprimer une réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    reservation_delete($pdo, (int)$_POST['supprimer_id']);
    redirect('/admin/reservations');
}

$statut = $_GET['statut'] ?? '';
if (!array_key_exists($statut, RESERVATION_STATUTS)) {
    $statut = '';
}

echo render($base . '/views/reservations.php', [
    'reservations' => reservation_get_all($pdo, $statut),
    'statut'       => $statut,
    'statuts'      => RESERVATION_STATUTS,
    'pageTitle'    => 'Réservations – Admin',
]);
