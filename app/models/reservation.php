<?php
// app/models/reservation.php
// Requêtes SQL liées aux demandes de réservation

const RESERVATION_HEURES  = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
const RESERVATION_STATUTS = ['en_attente' => 'En attente', 'confirmee' => 'Confirmée', 'annulee' => 'Annulée'];

// Horaires d'ouverture par jour (1 = lundi … 7 = dimanche), null = fermé
const RESERVATION_OUVERTURE = [
    1 => ['09:00', '19:00'], 2 => ['09:00', '19:00'], 3 => ['09:00', '19:00'],
    4 => ['09:00', '19:00'], 5 => ['09:00', '19:00'], 6 => ['10:00', '17:00'], 7 => null,
];

// Convertit "HH:MM" ou "HH:MM:SS" en minutes depuis minuit
function reservation_minutes(string $heure): int
{
    [$h, $m] = explode(':', $heure);
    return (int)$h * 60 + (int)$m;
}

// Heures de début encore libres pour un soin de $duree minutes à la date donnée.
// Un créneau est pris si un rendez-vous non annulé le chevauche (en tenant compte des durées),
// ou si le soin finirait après la fermeture.
function reservation_heures_disponibles(PDO $pdo, string $date, int $duree): array
{
    $horaires = RESERVATION_OUVERTURE[(int)date('N', strtotime($date))];
    if ($horaires === null) {
        return [];
    }
    [$ouverture, $fermeture] = array_map('reservation_minutes', $horaires);

    // Rendez-vous déjà pris ce jour-là (60 min par défaut si le soin a été supprimé)
    $occupes = query_all($pdo, "
        SELECT reservation.heure_rdv, COALESCE(item.duree, 60) AS duree
        FROM reservation
        LEFT JOIN item ON reservation.item_id = item.id
        WHERE reservation.date_rdv = :date AND reservation.statut != 'annulee'
    ", ['date' => $date]);

    $libres = [];
    foreach (RESERVATION_HEURES as $heure) {
        $debut = reservation_minutes($heure);
        $fin   = $debut + $duree;
        if ($debut < $ouverture || $fin > $fermeture) {
            continue;
        }
        // Aujourd'hui : pas de créneau déjà passé
        if ($date === date('Y-m-d') && $debut <= reservation_minutes(date('H:i'))) {
            continue;
        }
        foreach ($occupes as $o) {
            $oDebut = reservation_minutes($o['heure_rdv']);
            if ($debut < $oDebut + (int)$o['duree'] && $oDebut < $fin) {
                continue 2;
            }
        }
        $libres[] = $heure;
    }
    return $libres;
}

// Le montant est copié depuis le prix du soin, pour ne pas changer si le prix change plus tard
function reservation_insert(PDO $pdo, array $data): int
{
    query_run($pdo, "
        INSERT INTO reservation (nom, prenom, email, telephone, item_id, date_rdv, heure_rdv, preference_contact, remarques, montant)
        VALUES (:nom, :prenom, :email, :telephone, :item_id, :date_rdv, :heure_rdv, :preference_contact, :remarques,
                (SELECT prix FROM item WHERE id = :item_id_prix))
    ", $data + ['item_id_prix' => $data['item_id']]);
    return (int)query_last_id($pdo);
}

function reservation_get_by_id(PDO $pdo, int $id): array|false
{
    return query_one($pdo, "
        SELECT reservation.*, item.titre AS soin, item.duree
        FROM reservation
        LEFT JOIN item ON reservation.item_id = item.id
        WHERE reservation.id = :id
    ", ['id' => $id]);
}

// Paiement simulé : marque la réservation comme payée
function reservation_set_payee(PDO $pdo, int $id): void
{
    query_run($pdo, "
        UPDATE reservation SET paiement = 'payee', date_paiement = NOW()
        WHERE id = :id AND paiement = 'non_payee'
    ", ['id' => $id]);
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

// Envoie au client l'email correspondant à l'étape : 'recue', 'payee', 'confirmee' ou 'annulee'
function reservation_envoyer_email(PDO $pdo, int $id, string $etape): void
{
    $r = reservation_get_by_id($pdo, $id);
    if (!$r) {
        return;
    }

    $montant = number_format((float)$r['montant'], 2, ',', ' ') . ' €';
    $detail  = "Soin : " . ($r['soin'] ?? '—') . ($r['duree'] ? " ({$r['duree']} min)" : '') . "\n"
             . "Date : " . date('d/m/Y', strtotime($r['date_rdv'])) . " à " . substr($r['heure_rdv'], 0, 5) . "\n"
             . "Montant : $montant";

    [$sujet, $message] = match ($etape) {
        'recue'     => ["Votre demande de réservation",
                        "Nous avons bien reçu votre demande de réservation. Nous vous contacterons rapidement pour la confirmer."],
        'payee'     => ["Paiement reçu pour votre réservation",
                        "Nous avons bien reçu votre paiement de $montant. Merci !"],
        'confirmee' => ["Votre rendez-vous est confirmé",
                        "Bonne nouvelle : votre rendez-vous est confirmé. Nous avons hâte de vous accueillir."],
        'annulee'   => ["Votre rendez-vous a été annulé",
                        "Votre rendez-vous a été annulé. N'hésitez pas à nous contacter ou à réserver un autre créneau."],
    };

    $texte = "Bonjour {$r['prenom']},\n\n$message\n\n$detail\n\n"
           . "À bientôt,\n" . mail_signature();

    mail_envoyer($r['email'], salon('nom') . " – $sujet", $texte);
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
