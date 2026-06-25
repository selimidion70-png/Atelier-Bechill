# 🧘 BE CHILL — Salon de Massage

Site web dynamique pour un salon de massage fictif, développé dans le cadre du projet d'atelier à l'EFP Bruxelles.

---

## 📌 Description

**BE CHILL** est un site web administrable développé en PHP avec une architecture MVC, connecté à une base de données MySQL. Il permet la consultation des soins, la recherche par catégorie/thème, l'envoi de messages de contact, et la gestion complète des contenus via un espace d'administration sécurisé.

---

## 🛠️ Technologies utilisées

- HTML5 / CSS3
- PHP 8+
- MySQL (via PDO)
- Architecture MVC (Model / View / Controller)
- Laragon (serveur local Apache + MySQL)

---

## 📁 Structure du projet

```
Atelier-Bechill/
├── index.php              ← Point d'entrée unique (router)
├── .htaccess              ← Redirige toutes les requêtes vers index.php
├── style.css              ← Feuille de style globale
│
├── config/
│   └── database.php       ← Connexion PDO à la base de données
│
├── core/
│   ├── http.php           ← Fonctions HTTP (http_in, http_out, redirect)
│   ├── router.php         ← Router (route, run)
│   ├── html.php           ← Fonction render (injection de vues)
│   └── query.php          ← Fonctions PDO réutilisables
│
└── app/
    ├── models/
    │   ├── item.php        ← Requêtes SQL sur les soins
    │   └── message.php     ← Requêtes SQL sur les messages
    │
    ├── controllers/
    │   ├── home.php        ← Page d'accueil
    │   ├── soins.php       ← Catalogue avec filtres
    │   ├── soin-detail.php ← Détail d'un soin
    │   └── contact.php     ← Formulaire de contact
    │
    ├── views/
    │   ├── _layout.php     ← Layout commun (header + nav + footer)
    │   ├── home.php        ← Vue accueil
    │   ├── soins.php       ← Vue catalogue
    │   ├── soin-detail.php ← Vue détail soin
    │   └── contact.php     ← Vue contact
    │
    └── admin/
        ├── controllers/
        │   ├── home.php    ← Dashboard admin
        │   ├── login.php   ← Authentification
        │   └── logout.php  ← Déconnexion
        └── views/
            ├── _layout.php ← Layout admin
            ├── login.php   ← Vue connexion
            └── dashboard.php ← Vue tableau de bord
```

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

1. Ouvrir **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Créer une base de données nommée `bechill` (utf8mb4_unicode_ci)
3. Importer le fichier `sql/schema.sql`

### 4. Configurer la connexion

Vérifier les paramètres dans `config/database.php` :

```php
$host     = 'localhost';
$dbname   = 'bechill';
$user     = 'root';
$password = ''; // vide par défaut sur Laragon
```

### 5. Générer le mot de passe admin

Ouvrir dans le navigateur :
```
http://bechill.be.add/admin/login
```

Identifiants par défaut :
- **Email** : `admin@bechill.be`
- **Mot de passe** : `admin123`

### 6. Lancer le site

Démarrer Laragon (Start All), puis ouvrir :
```
http://bechill.be.add
```

---

## 🌐 Pages disponibles

### Site public
| URL | Description |
|-----|-------------|
| `http://bechill.be.add/` | Page d'accueil |
| `http://bechill.be.add/soins` | Catalogue des soins avec filtres |
| `http://bechill.be.add/soin-detail?slug=massage-relaxant` | Détail d'un soin |
| `http://bechill.be.add/contact` | Formulaire de contact |

### Espace admin
| URL | Description |
|-----|-------------|
| `http://bechill.be.add/admin/login` | Connexion admin |
| `http://bechill.be.add/admin` | Tableau de bord |
| `http://bechill.be.add/admin/soins` | Gestion des soins |
| `http://bechill.be.add/admin/messages` | Messages de contact |

---

## 🗃️ Base de données

| Table | Description |
|-------|-------------|
| `operator` | Utilisateurs et administrateurs |
| `item` | Les soins (titre, slug, description, prix, durée) |
| `category` | Catégories (Massage classique, Massage spécifique) |
| `theme` | Thèmes (Relaxation, Récupération sportive, Bien-être ciblé) |
| `tag` | Mots-clés associés aux soins |
| `item_tag` | Liaison soins ↔ tags |
| `message` | Messages envoyés via le formulaire de contact |
| `collection` | Collections personnalisées d'items |
| `collection_item` | Liaison collections ↔ items |

---

## 👤 Auteur

**Dion Selimi**
- GitHub : [selimidion70-png](https://github.com/selimidion70-png)
- Email : selimidion70@gmail.com
- Formation : Développeur Web Front-End — EFP Bruxelles (2025–2027)
