# 🧘 BE CHILL — Salon de Massage

Site web dynamique pour un salon de massage fictif, développé dans le cadre du projet d'atelier à l'EFP Bruxelles.

---

## 📌 Description

**BE CHILL** est un site web administrable développé en PHP avec une architecture MVC, connecté à une base de données MySQL.

**Site public**
- Catalogue des soins avec recherche (titre, description, tags) et filtres (catégorie, thème, durée)
- Fiche détaillée de chaque soin, avec photo
- Page tarifs générée depuis la base de données
- Réservation en ligne : les créneaux déjà pris et les horaires d'ouverture sont respectés
- Paiement en ligne avec **Mollie** (Bancontact, cartes, Apple Pay…) ou paiement sur place ; sans clé Mollie, le paiement est **simulé** (démonstration)
- Pages **Mentions légales** et **Politique de confidentialité** (RGPD)
- Emails de confirmation au client
- Sélections de soins (collections) sur la page d'accueil
- Formulaire de contact

**Espace d'administration**
- Tableau de bord
- Soins : ajout, modification, photo, tags, publication, suppression
- Catégories, thèmes et tags
- Collections de soins
- Réservations : confirmer, annuler, suivi des paiements
- Messages de contact
- Utilisateurs avec deux rôles : **administrateur** et **éditeur**, et « mot de passe oublié » par email
- RGPD : suppression automatique des anciennes données, recherche et suppression des données d'un client

---

## 🛠️ Technologies utilisées

- HTML5 / CSS3 / un peu de JavaScript (créneaux de réservation)
- PHP 8+
- MySQL (via PDO, requêtes préparées)
- Architecture MVC (Model / View / Controller)
- Laragon (Apache + MySQL + Mailpit)

---

## 📁 Structure du projet

```
Atelier-Bechill/
├── index.php              ← Point d'entrée unique (routeur, droits d'accès admin, CSRF)
├── .htaccess              ← Redirige vers index.php et bloque les dossiers internes
├── style.css              ← Feuille de style globale
│
├── config/
│   ├── app.php            ← TOUS les réglages : salon, URL, base, emails, Mollie, RGPD
│   ├── app.local.exemple.php ← Modèle pour app.local.php (secrets, ignoré par git)
│   └── database.php       ← Connexion PDO à la base de données
│
├── core/
│   ├── http.php           ← Fonctions HTTP (http_in, http_out, redirect)
│   ├── router.php         ← Routeur (route, run)
│   ├── html.php           ← Fonction render (injection de vues)
│   ├── query.php          ← Fonctions PDO réutilisables
│   ├── csrf.php           ← Jeton CSRF des formulaires POST
│   ├── config.php         ← Lecture des réglages : config('…'), salon('…')
│   ├── mail.php           ← Envoi d'emails (UTF-8)
│   └── mollie.php         ← Paiement en ligne (API Mollie)
│
├── app/
│   ├── models/
│   │   ├── item.php        ← Soins (+ photos, slug, validation du formulaire)
│   │   ├── taxonomie.php   ← Catégories, thèmes, tags
│   │   ├── collection.php  ← Collections de soins
│   │   ├── reservation.php ← Réservations (créneaux, paiement, emails)
│   │   ├── message.php     ← Messages de contact
│   │   ├── operator.php    ← Comptes admin / éditeur (+ mot de passe oublié)
│   │   └── rgpd.php        ← Durée de conservation, données d'un client
│   │
│   ├── controllers/        ← Une page publique = un controller
│   │   └── home, soins, soin-detail, tarifs, reservation, paiement, paiement-webhook,
│   │       apropos, contact, mentions-legales, confidentialite
│   ├── views/              ← Une vue par controller + _layout.php (header, nav, footer)
│   │
│   └── admin/
│       ├── acces.php       ← Rôles et pages réservées à l'administrateur
│       ├── controllers/    ← home, login, logout, soins, soins-ajouter, soins-modifier,
│       │                     categories, themes, tags (_taxonomie partagé), collections,
│       │                     collections-modifier, reservations, messages, utilisateurs,
│       │                     donnees-client, mot-de-passe-oublie, reinitialiser
│       └── views/          ← Une vue par page + _layout.php et _soin-champs.php (formulaire soin)
│
├── uploads/soins/         ← Photos envoyées depuis l'admin (seules les images sont servies)
├── sql/
│   └── schema.sql         ← Structure + données de départ de la base
└── _archives/             ← Anciennes versions (maquettes HTML, PHP avant MVC) — non accessibles
```

Les fichiers dont le nom commence par `_` sont internes : le routeur refuse de les ouvrir comme des pages.

---

## 🚀 Installation

### 1. Prérequis

- [Laragon](https://laragon.org/) installé (Apache + MySQL + PHP 8+)

### 2. Cloner le projet

```bash
git clone https://github.com/selimidion70-png/Atelier-Bechill.git
```

Placer le dossier dans :
```
C:\laragon\www\Atelier-Bechill\
```

### 3. Créer la base de données

1. Ouvrir **HeidiSQL** ou **phpMyAdmin**
2. Importer le fichier `sql/schema.sql` : il crée la base `bechill`, les tables et les données de départ

> ⚠️ `schema.sql` **supprime puis recrée** toutes les tables : ne le réimportez pas sur une base dont vous voulez garder les données.

### 4. Réglages

Tous les réglages sont dans **`config/app.php`** : coordonnées du salon, adresse du site, base de données, expéditeur des emails, durées RGPD. Les valeurs par défaut fonctionnent directement avec Laragon.

Les valeurs **secrètes** (clé Mollie, mot de passe de la base en ligne) vont dans **`config/app.local.php`** : copiez `config/app.local.exemple.php` et remplissez-le. Ce fichier est ignoré par git, il ne part jamais sur GitHub.

### 5. Lancer le site

Démarrer Laragon (**Start All**), puis ouvrir :
```
http://atelier-bechill.test
```

Laragon crée automatiquement l'adresse `nom-du-dossier.test`.

### 6. Se connecter à l'administration

```
http://atelier-bechill.test/admin
```

Identifiants par défaut :
- **Email** : `admin@bechill.be`
- **Mot de passe** : `admin123`

### 7. Voir les emails envoyés

En local, les emails ne partent pas vraiment : **Mailpit** (fourni avec Laragon) les capture. Pour les lire :
```
http://localhost:8025
```

---

## 🌐 Pages disponibles

### Site public
| URL | Description |
|-----|-------------|
| `/` | Accueil : soins vedettes, sélections (collections), horaires |
| `/soins` | Catalogue des soins avec recherche et filtres |
| `/soin-detail?slug=massage-relaxant` | Détail d'un soin |
| `/tarifs` | Tarifs (depuis la base de données) |
| `/reservation` | Réservation d'un soin (créneaux libres uniquement) |
| `/paiement` | Paiement simulé de la réservation qui vient d'être faite |
| `/apropos` | À propos |
| `/contact` | Formulaire de contact |
| `/mentions-legales` | Mentions légales |
| `/confidentialite` | Politique de confidentialité (RGPD) |

### Espace admin
| URL | Description | Rôle |
|-----|-------------|------|
| `/admin/login` | Connexion | — |
| `/admin` | Tableau de bord | éditeur, admin |
| `/admin/soins` | Gestion des soins | éditeur, admin |
| `/admin/categories`, `/admin/themes`, `/admin/tags` | Catégories, thèmes, tags | éditeur, admin |
| `/admin/collections` | Collections de soins | éditeur, admin |
| `/admin/reservations` | Réservations et paiements | admin |
| `/admin/messages` | Messages de contact | admin |
| `/admin/utilisateurs` | Comptes et rôles | admin |
| `/admin/donnees-client` | RGPD : voir / supprimer les données d'un client | admin |
| `/admin/mot-de-passe-oublie` | Recevoir un lien pour changer son mot de passe | — |

---

## 🗃️ Base de données

| Table | Description |
|-------|-------------|
| `operator` | Comptes de l'administration (rôle `admin` ou `editeur`, actif ou non, lien « mot de passe oublié ») |
| `item` | Les soins (titre, slug, descriptions, photo, durée, prix, statut) |
| `category` | Catégories (Massage classique, Massage spécifique) |
| `theme` | Thèmes (Relaxation, Récupération sportive, Bien-être ciblé) |
| `tag` | Mots-clés associés aux soins |
| `item_tag` | Liaison soins ↔ tags |
| `collection` | Sélections de soins créées dans l'admin |
| `collection_item` | Liaison collections ↔ soins |
| `reservation` | Demandes de réservation (créneau, statut, montant, paiement, identifiant Mollie) |
| `message` | Messages envoyés via le formulaire de contact |

---

## 🔒 Sécurité

- Requêtes SQL préparées (PDO) partout
- Mots de passe hachés (`password_hash` / `password_verify`)
- Jeton **CSRF** sur tous les formulaires POST
- Rôles vérifiés à chaque page de l'admin (y compris les envois de formulaire) ; un compte désactivé est déconnecté immédiatement
- Identifiant de session renouvelé à la connexion ; cookie de session HttpOnly, SameSite (et Secure en production)
- « Mot de passe oublié » : lien valable 1 heure, utilisable une fois, seul son hash est stocké
- En production : erreurs PHP masquées aux visiteurs
- Photos : type réel vérifié, 3 Mo max, nom de fichier aléatoire, aucun script exécutable dans `uploads/`
- Accès direct bloqué à `app/`, `core/`, `config/`, `sql/`, `.git/`, `_archives/`
- Paiement : les données de carte sont saisies chez Mollie, jamais sur le site ; le statut est toujours revérifié auprès de Mollie

---

## 💳 Activer le paiement Mollie

1. Créer un compte gratuit sur [mollie.com](https://www.mollie.com)
2. Tableau de bord Mollie → **Développeurs → Clés API** : copier la clé **test** (`test_…`)
3. Copier `config/app.local.exemple.php` en `config/app.local.php` et y coller la clé :
   ```php
   'mollie_cle' => 'test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
   ```
4. Faire une réservation : le bouton « Payer » envoie sur la page de paiement Mollie (mode test : aucun vrai argent)

En local, Mollie ne peut pas prévenir le site (webhook) : le statut est vérifié quand le client revient sur le site. Une fois en ligne en `https`, le webhook est activé automatiquement.
Pour encaisser pour de vrai : faire valider le compte par Mollie, puis remplacer la clé par la clé `live_…`. Apple Pay demande en plus de valider le nom de domaine dans le tableau de bord Mollie.

---

## 🌍 Mise en ligne (pour un vrai client)

1. **Hébergement** : un hébergeur PHP 8 + MySQL avec HTTPS (certificat SSL) et un nom de domaine (ex. `nom-du-salon.be`)
2. **Fichiers** : envoyer le projet (sans `_archives/` si vous voulez) ; le dossier `uploads/soins/` doit être accessible en écriture par PHP
3. **Base de données** : créer la base chez l'hébergeur, importer `sql/schema.sql`, puis **changer tout de suite le mot de passe admin** (`admin123`) via « Utilisateurs »
4. **`config/app.local.php`** sur le serveur :
   ```php
   return [
       'environnement' => 'production',
       'url_site'      => 'https://www.nom-du-salon.be',
       'base_de_donnees' => ['hote' => '…', 'nom' => '…', 'utilisateur' => '…', 'mot_de_passe' => '…'],
       'mollie_cle'    => 'live_…',
   ];
   ```
5. **`config/app.php`** : coordonnées réelles du salon, forme juridique, numéro BCE, hébergeur, adresse d'expéditeur des emails (sur le domaine du salon)
6. **Emails** : vérifier que l'hébergeur envoie les emails de PHP (`mail()`) et configurer SPF/DKIM pour le domaine, sinon ils risquent d'arriver en spam
7. **Contenu** : vrais textes, photos, horaires (`RESERVATION_OUVERTURE` dans `app/models/reservation.php`), soins et prix
8. **Juridique** : faire relire les mentions légales et la politique de confidentialité par le client
9. **Sauvegardes** : activer les sauvegardes automatiques de la base et du dossier `uploads/` chez l'hébergeur

---

## 👤 Auteur

**Dion Selimi**
- GitHub : [selimidion70-png](https://github.com/selimidion70-png)
- Email : selimidion70@gmail.com
- Formation : Développeur Web Front-End — EFP Bruxelles (2025–2027)
