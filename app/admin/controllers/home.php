<?php
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../../models/message.php';
require_once __DIR__ . '/../../models/reservation.php';
require_once __DIR__ . '/../../models/rgpd.php';

// RGPD : suppression automatique des données trop anciennes (à chaque ouverture du tableau de bord)
rgpd_purger($pdo);

$nbSoinsActifs = query_one($pdo, "SELECT COUNT(*) AS n FROM item WHERE statut = 'publie'")['n'];
$nbSoinsTotal = query_one($pdo, "SELECT COUNT(*) AS n FROM item")['n'];
$nbMessagesNonLus = message_count_unread($pdo);
$nbReservationsEnAttente = reservation_count_en_attente($pdo);

$derniersSoins = query_all($pdo, "SELECT item.titre, item.date_creation, category.nom AS categorie, item.statut FROM item JOIN category ON item.category_id = category.id ORDER BY item.date_creation DESC LIMIT 5");

echo render($base . '/views/dashboard.php', ['nbSoinsActifs' => $nbSoinsActifs, 'nbSoinsTotal' => $nbSoinsTotal, 'nbMessagesNonLus' => $nbMessagesNonLus, 'nbReservationsEnAttente' => $nbReservationsEnAttente, 'derniersSoins' => $derniersSoins, 'pageTitle' => 'Tableau de bord']);
