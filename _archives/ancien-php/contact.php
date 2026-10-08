<?php
require_once __DIR__ . '/db.php';

$erreurs = [];
$succes = false;

$valeurs = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'telephone' => '',
    'sujet' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs['nom'] = trim($_POST['nom'] ?? '');
    $valeurs['prenom'] = trim($_POST['prenom'] ?? '');
    $valeurs['email'] = trim($_POST['email'] ?? '');
    $valeurs['telephone'] = trim($_POST['telephone'] ?? '');
    $valeurs['sujet'] = $_POST['sujet'] ?? '';
    $valeurs['message'] = trim($_POST['message'] ?? '');

    // --- Validation ---
    if ($valeurs['nom'] === '') {
        $erreurs[] = "Le nom est obligatoire.";
    }
    if ($valeurs['prenom'] === '') {
        $erreurs[] = "Le prénom est obligatoire.";
    }
    if ($valeurs['email'] === '' || !filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Veuillez saisir une adresse courriel valide.";
    }
    if ($valeurs['sujet'] === '') {
        $erreurs[] = "Veuillez choisir un sujet.";
    }
    if ($valeurs['message'] === '') {
        $erreurs[] = "Le message ne peut pas être vide.";
    }

    if (empty($erreurs)) {
        // Le nom complet (prénom + nom) est stocké dans message.nom
        $nomComplet = $valeurs['prenom'] . ' ' . $valeurs['nom'];

        // Le téléphone (s'il est renseigné) est ajouté au texte du message,
        // car la table "message" ne prévoit pas de colonne dédiée
        $texte = $valeurs['message'];
        if ($valeurs['telephone'] !== '') {
            $texte .= "\n\nTéléphone communiqué : " . $valeurs['telephone'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO message (nom, email, sujet, texte, lu)
            VALUES (:nom, :email, :sujet, :texte, 0)
        ");
        $stmt->execute([
            'nom' => $nomComplet,
            'email' => $valeurs['email'],
            'sujet' => $valeurs['sujet'],
            'texte' => $texte,
        ]);

        $succes = true;

        // Réinitialisation du formulaire après envoi réussi
        $valeurs = [
            'nom' => '',
            'prenom' => '',
            'email' => '',
            'telephone' => '',
            'sujet' => '',
            'message' => '',
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact – BE CHILL</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <p>BE CHILL</p>
        <p>Salon de massage et bien-être à Bruxelles</p>
    </header>

    <nav aria-label="Navigation principale">
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="soins.php">Nos soins</a></li>
            <li><a href="tarifs.html">Tarifs</a></li>
            <li><a href="reservation.html">Réservation</a></li>
            <li><a href="apropos.html">À propos</a></li>
            <li><a href="contact.php" aria-current="page">Contact</a></li>
        </ul>
        <form action="soins.php" method="get" role="search" aria-label="Recherche de soins">
            <label for="recherche-nav">Rechercher un soin</label>
            <input type="search" id="recherche-nav" name="recherche" placeholder="Ex : relaxant, sportif…">
            <button type="submit">Rechercher</button>
        </form>
    </nav>

    <main>
        <h1>Contactez-nous</h1>
        <p>Une question, une remarque ou une demande particulière ? N'hésitez pas à nous écrire via le formulaire ci-dessous ou à nous contacter directement.</p>

        <?php if ($succes): ?>
            <p role="status">Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.</p>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div role="alert" style="color: red;">
                <p>Veuillez corriger les erreurs suivantes :</p>
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?= htmlspecialchars($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section aria-labelledby="formulaire-contact">
            <h2 id="formulaire-contact">Formulaire de contact</h2>

            <form action="contact.php" method="post">
                <fieldset>
                    <legend>Vos coordonnées</legend>

                    <p>
                        <label for="nom">Nom</label><br>
                        <input type="text" id="nom" name="nom" autocomplete="family-name" required
                               value="<?= htmlspecialchars($valeurs['nom']) ?>">
                    </p>

                    <p>
                        <label for="prenom">Prénom</label><br>
                        <input type="text" id="prenom" name="prenom" autocomplete="given-name" required
                               value="<?= htmlspecialchars($valeurs['prenom']) ?>">
                    </p>

                    <p>
                        <label for="email">Adresse courriel</label><br>
                        <input type="email" id="email" name="email" autocomplete="email" required
                               value="<?= htmlspecialchars($valeurs['email']) ?>">
                    </p>

                    <p>
                        <label for="telephone">Numéro de téléphone</label><br>
                        <input type="tel" id="telephone" name="telephone" autocomplete="tel"
                               value="<?= htmlspecialchars($valeurs['telephone']) ?>">
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Votre message</legend>

                    <p>
                        <label for="sujet">Sujet</label><br>
                        <select id="sujet" name="sujet" required>
                            <option value="">-- Choisissez un sujet --</option>
                            <option value="information" <?= $valeurs['sujet'] === 'information' ? 'selected' : '' ?>>Demande d'information</option>
                            <option value="reservation" <?= $valeurs['sujet'] === 'reservation' ? 'selected' : '' ?>>Question sur une réservation</option>
                            <option value="reclamation" <?= $valeurs['sujet'] === 'reclamation' ? 'selected' : '' ?>>Réclamation</option>
                            <option value="partenariat" <?= $valeurs['sujet'] === 'partenariat' ? 'selected' : '' ?>>Proposition de partenariat</option>
                            <option value="autre" <?= $valeurs['sujet'] === 'autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </p>

                    <p>
                        <label for="message">Message</label><br>
                        <textarea id="message" name="message" rows="6" cols="50" required><?= htmlspecialchars($valeurs['message']) ?></textarea>
                    </p>
                </fieldset>

                <p>
                    <button type="submit">Envoyer le message</button>
                    <button type="reset">Effacer le formulaire</button>
                </p>
            </form>
        </section>

        <section aria-labelledby="nos-coordonnees">
            <h2 id="nos-coordonnees">Nos coordonnées</h2>
            <address>
                BE CHILL – Salon de massage<br>
                Rue de la Détente 10<br>
                1000 Bruxelles
            </address>

            <h3>Par téléphone</h3>
            <p><a href="tel:+32470000000">+32 470 00 00 00</a></p>
            <p>Disponible du lundi au vendredi, de 9h00 à 19h00.</p>

            <h3>Par courriel</h3>
            <p><a href="mailto:info@bechill.be">info@bechill.be</a></p>
            <p>Nous répondons dans un délai de 24 heures ouvrables.</p>
        </section>

        <section aria-labelledby="horaires-contact">
            <h2 id="horaires-contact">Horaires d'ouverture</h2>
            <table>
                <caption>Horaires d'ouverture de BE CHILL</caption>
                <thead>
                    <tr>
                        <th scope="col">Jour</th>
                        <th scope="col">Horaire</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Lundi – Vendredi</td>
                        <td>9h00 – 19h00</td>
                    </tr>
                    <tr>
                        <td>Samedi</td>
                        <td>10h00 – 17h00</td>
                    </tr>
                    <tr>
                        <td>Dimanche</td>
                        <td>Fermé</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section aria-labelledby="acces">
            <h2 id="acces">Comment nous trouver</h2>
            <h3>En transports en commun</h3>
            <ul>
                <li>Métro : arrêt De Brouckère (lignes 1 et 5)</li>
                <li>Tram : arrêt Bourse (lignes 3 et 4)</li>
                <li>Bus : arrêt Anspach (lignes 46 et 86)</li>
            </ul>

            <h3>En voiture</h3>
            <p>Parking public disponible à 200 mètres (Parking Monnaie).</p>
        </section>
    </main>

    <footer>
        <section aria-labelledby="coordonnees-footer-contact">
            <h2 id="coordonnees-footer-contact">Coordonnées</h2>
            <address>
                BE CHILL – Salon de massage<br>
                Rue de la Détente 10<br>
                1000 Bruxelles<br>
                Téléphone : <a href="tel:+32470000000">+32 470 00 00 00</a><br>
                Courriel : <a href="mailto:info@bechill.be">info@bechill.be</a>
            </address>
        </section>
        <nav aria-label="Navigation secondaire">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="soins.php">Nos soins</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
        <p><small>&copy; 2026 BE CHILL – Tous droits réservés</small></p>
    </footer>

</body>
</html>
