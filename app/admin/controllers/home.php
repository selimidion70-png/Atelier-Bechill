<?php
require_once __DIR__ . '/../../models/item.php';
require_once __DIR__ . '/../../models/message.php';

$nbSoinsActifs = query_one($pdo, "SELECT COUNT(*) AS n FROM item WHERE statut = 'publie'")['n'];
$nbSoinsTotal = query_one($pdo, "SELECT COUNT(*) AS n FROM item")['n'];
$nbMessagesNonLus = message_count_unread($pdo);

$derniersSoins = query_all($pdo, "SELECT item.titre, item.date_creation, category.nom AS categorie, item.statut FROM item JOIN category ON item.category_id = category.id ORDER BY item.date_creation DESC LIMIT 5");

echo render($base . '/views/dashboard.php', ['nbSoinsActifs' => $nbSoinsActifs, 'nbSoinsTotal' => $nbSoinsTotal, 'nbMessagesNonLus' => $nbMessagesNonLus, 'derniersSoins' => $derniersSoins, 'pageTitle' => 'Tableau de bord']);
