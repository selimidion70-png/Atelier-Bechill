<?php
// config/app.php
// Réglages du site. Pour un autre salon ou pour la mise en ligne, c'est ici qu'on change tout.
//
// Les valeurs SECRÈTES (clé Mollie, mots de passe…) ne vont PAS ici : mettez-les dans
// config/app.local.php (même format, ignoré par git). Il remplace les valeurs ci-dessous.

return [
    // 'local' = sur votre ordinateur (erreurs PHP affichées) ; 'production' = sur le serveur
    'environnement' => 'local',

    // Base de données (valeurs par défaut de Laragon)
    'base_de_donnees' => [
        'hote'         => 'localhost',
        'nom'          => 'bechill',
        'utilisateur'  => 'root',
        'mot_de_passe' => '',
    ],

    // Adresse publique du site, sans « / » à la fin (utilisée dans les emails et pour Mollie)
    'url_site' => 'http://atelier-bechill.test',

    // Coordonnées du salon (pied de page, emails, mentions légales)
    'salon' => [
        'nom'           => 'BE CHILL',
        'activite'      => 'Salon de massage',
        'adresse'       => 'Rue de la Détente 10',
        'code_postal'   => '1000',
        'ville'         => 'Bruxelles',
        'telephone'     => '+32 470 00 00 00',
        'email'         => 'info@bechill.be',
        // Mentions légales (Belgique) : à remplacer par les vraies informations du client
        'forme_juridique' => 'SRL (fictive – projet d\'école)',
        'numero_bce'      => '0000.000.000',
        'responsable'     => 'Dion Selimi',
    ],

    // Expéditeur des emails envoyés aux clients
    'email_expediteur' => 'BE CHILL <info@bechill.be>',

    // Paiement en ligne avec Mollie (https://www.mollie.com).
    // Clé vide = paiement SIMULÉ (démonstration). Clé « test_… » = vrai parcours Mollie sans vrai argent.
    'mollie_cle' => '',

    // RGPD : durée de conservation des données des clients (en mois), puis suppression automatique
    'rgpd_conservation_reservations' => 24,
    'rgpd_conservation_messages'     => 12,
];
