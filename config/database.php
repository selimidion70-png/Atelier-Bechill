<?php
// config/database.php
// Connexion à la base de données via PDO.
// Les paramètres sont dans config/app.php (« base_de_donnees ») ; en ligne, mettez le vrai
// mot de passe dans config/app.local.php pour qu'il ne parte pas sur GitHub.

$bdd = config('base_de_donnees');

try {
    $pdo = new PDO(
        "mysql:host={$bdd['hote']};dbname={$bdd['nom']};charset=utf8mb4",
        $bdd['utilisateur'],
        $bdd['mot_de_passe'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log('Connexion BDD impossible : ' . $e->getMessage());
    die(config('environnement') === 'production'
        ? 'Le site est momentanément indisponible. Merci de réessayer plus tard.'
        : 'Erreur de connexion : ' . $e->getMessage());
}
