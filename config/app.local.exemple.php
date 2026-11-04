<?php
// config/app.local.exemple.php
// Copiez ce fichier en « app.local.php » et remplissez-le.
// app.local.php est ignoré par git : vos secrets ne partent jamais sur GitHub.
// Seules les clés présentes ici remplacent celles de config/app.php.

return [
    // Clé secrète Stripe (tableau de bord Stripe → Développeurs → Clés API). Commence par « sk_test_ » ou « sk_live_ ».
    'stripe_cle' => 'COLLER_ICI_VOTRE_CLE_SK_TEST',
    // Secret du webhook (Développeurs → Webhooks). Inutile en local : le statut est vérifié au retour du client.
    'stripe_webhook_secret' => '',

    // Exemple pour la mise en ligne :
    // 'environnement' => 'production',
    // 'url_site'      => 'https://www.nom-du-salon.be',
    // 'base_de_donnees' => [
    //     'hote'         => 'localhost',
    //     'nom'          => 'nom_de_la_base',
    //     'utilisateur'  => 'utilisateur_mysql',
    //     'mot_de_passe' => 'mot_de_passe_mysql',
    // ],
];
