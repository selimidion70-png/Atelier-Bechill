<?php
// app/models/reservation.php
// Requêtes SQL liées aux demandes de réservation

const RESERVATION_HEURES  = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
const RESERVATION_STATUTS = ['en_attente' => 'En attente', 'confirmee' => 'Confirmée', 'annulee' => 'Annulée'];

function reservation_insert(PDO $pdo, array $data): void
{
    query_run($pdo, "
        INSERT INTO reservation (nom, prenom, email, telephone, item_id, date_rdv, heure_rdv, preference_contact, remarques)
        VALUES (:nom, :prenom, :email, :telephone, :item_id, :date_rdv, :heure_rdv, :preference_contact, :remarques)
    ", $data);
}

function reservation_get_all(PDO $pdo, string $statut = ''): array
{
    $sql = "
        SELECT reservation.*, item.titre AS soin, item.duree
        FROM reservation
        LEFT JOIN item ON reservation.item_id = item.id
    ";
    $params = [];

    if ($statut !== '') {
        $sql .= " WHERE reservation.statut = :statut";
        $params['statut'] = $statut;
    }

    $sql .= " ORDER BY reservation.date_rdv, reservation.heure_rdv";

    return query_all($pdo, $sql, $params);
}

function reservation_set_statut(PDO $pdo, int $id, string $statut): void
{
    query_run($pdo, "UPDATE reservation SET statut = :statut WHERE id = :id", ['statut' => $statut, 'id' => $id]);
}

function reservation_delete(PDO $pdo, int $id): void
{
    query_run($pdo, "DELETE FROM reservation WHERE id = :id", ['id' => $id]);
}

function reservation_count_en_attente(PDO $pdo): int
{
    $result = query_one($pdo, "SELECT COUNT(*) AS total FROM reservation WHERE statut = 'en_attente'");
    return (int)$result['total'];
}
