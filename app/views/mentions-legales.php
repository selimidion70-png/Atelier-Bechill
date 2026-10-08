<?php
// Modèle de mentions légales (Belgique, Code de droit économique).
// Les informations viennent de config/app.php : à remplacer par celles du vrai client
// et à faire relire avant la mise en ligne.
?>
<main>
    <h1>Mentions légales</h1>

    <section aria-labelledby="editeur">
        <h2 id="editeur">Éditeur du site</h2>
        <address>
            <?= htmlspecialchars(salon('nom')) ?> – <?= htmlspecialchars(salon('forme_juridique')) ?><br>
            <?= htmlspecialchars(salon('adresse')) ?>, <?= htmlspecialchars(salon('code_postal') . ' ' . salon('ville')) ?><br>
            Numéro d'entreprise (BCE) : <?= htmlspecialchars(salon('numero_bce')) ?><br>
            Téléphone : <a href="tel:<?= preg_replace('/[^0-9+]/', '', salon('telephone')) ?>"><?= str_replace(' ', '&nbsp;', htmlspecialchars(salon('telephone'))) ?></a><br>
            Courriel : <a href="mailto:<?= htmlspecialchars(salon('email')) ?>"><?= htmlspecialchars(salon('email')) ?></a>
        </address>
        <p>Responsable de la publication : <?= htmlspecialchars(salon('responsable')) ?></p>
    </section>

    <section aria-labelledby="hebergement">
        <h2 id="hebergement">Hébergement</h2>
        <p><?= htmlspecialchars(salon('hebergeur')) ?></p>
    </section>

    <section aria-labelledby="propriete">
        <h2 id="propriete">Propriété intellectuelle</h2>
        <p>Les textes, photos et éléments graphiques de ce site appartiennent à <?= htmlspecialchars(salon('nom')) ?>, sauf mention contraire. Toute reproduction sans autorisation est interdite.</p>
    </section>

    <section aria-labelledby="donnees">
        <h2 id="donnees">Données personnelles</h2>
        <p>La manière dont nous utilisons vos données est expliquée dans notre <a href="/confidentialite">politique de confidentialité</a>.</p>
    </section>
</main>
