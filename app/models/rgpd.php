<?php
// app/models/rgpd.php
// Protection des données des clients (RGPD) : durée de conservation et droits des personnes

// Supprime les données plus anciennes que les durées de config/app.php.
// Une réservation est gardée N mois après la date du rendez-vous, un message N mois après son envoi.
// Retourne le nombre de lignes supprimées.
function rgpd_purger(PDO $pdo): int
{
    $moisReservations = (int)config('rgpd_conservation_reservations');
    $moisMessages     = (int)config('rgpd_conservation_messages');

    $r = $pdo->prepare("DELETE FROM reservation WHERE date_rdv < DATE_SUB(CURDATE(), INTERVAL :mois MONTH)");
    $r->execute(['mois' => $moisReservations]);

    $m = $pdo->prepare("DELETE FROM message WHERE date_envoi < DATE_SUB(NOW(), INTERVAL :mois MONTH)");
    $m->execute(['mois' => $moisMessages]);

    return $r->rowCount() + $m->rowCount();
}

// Tout ce que le site garde sur une personne (droit d'accès)
function rgpd_donnees_client(PDO $pdo, string $email): array
{
    return [
        'reservations' => query_all($pdo, "
            SELECT reservation.*, item.titre AS soin
            FROM reservation LEFT JOIN item ON reservation.item_id = item.id
            WHERE reservation.email = :email
            ORDER BY reservation.date_rdv DESC
        ", ['email' => $email]),
        'messages' => query_all($pdo, "
            SELECT * FROM message WHERE email = :email ORDER BY date_envoi DESC
        ", ['email' => $email]),
    ];
}

// Efface toutes les données d'une personne (droit à l'effacement)
function rgpd_supprimer_client(PDO $pdo, string $email): void
{
    query_run($pdo, "DELETE FROM reservation WHERE email = :email", ['email' => $email]);
    query_run($pdo, "DELETE FROM message WHERE email = :email", ['email' => $email]);
}
