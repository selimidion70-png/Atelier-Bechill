-- ============================================================
-- BE CHILL - sql/schema.sql
-- Base de données reconstruite à partir du code PHP du projet.
-- Import : exécuter ce fichier dans HeidiSQL ou phpMyAdmin.
-- Admin par défaut : admin@bechill.be / admin123
-- ============================================================

CREATE DATABASE IF NOT EXISTS bechill
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bechill;
SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS collection_item;
DROP TABLE IF EXISTS collection;
DROP TABLE IF EXISTS item_tag;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS item;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS theme;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS operator;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- Utilisateurs et administrateurs
-- ------------------------------------------------------------
CREATE TABLE operator (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe  VARCHAR(255) NOT NULL,
    role          ENUM('admin', 'editeur', 'membre') NOT NULL DEFAULT 'membre',
    actif         TINYINT(1) NOT NULL DEFAULT 1,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Catégories, thèmes, tags
-- ------------------------------------------------------------
CREATE TABLE category (
    id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE theme (
    id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tag (
    id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Soins
-- ------------------------------------------------------------
CREATE TABLE item (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre              VARCHAR(150) NOT NULL,
    slug               VARCHAR(160) NOT NULL UNIQUE,
    description_courte VARCHAR(255) NOT NULL,
    description        TEXT NOT NULL,
    duree              SMALLINT UNSIGNED NOT NULL,
    prix               DECIMAL(6,2) NOT NULL,
    statut             ENUM('brouillon', 'publie', 'archive') NOT NULL DEFAULT 'brouillon',
    operator_id        INT UNSIGNED NULL,
    theme_id           INT UNSIGNED NOT NULL,
    category_id        INT UNSIGNED NOT NULL,
    date_creation      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_item_operator FOREIGN KEY (operator_id) REFERENCES operator(id) ON DELETE SET NULL,
    CONSTRAINT fk_item_theme    FOREIGN KEY (theme_id)    REFERENCES theme(id),
    CONSTRAINT fk_item_category FOREIGN KEY (category_id) REFERENCES category(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE item_tag (
    item_id INT UNSIGNED NOT NULL,
    tag_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (item_id, tag_id),
    CONSTRAINT fk_itemtag_item FOREIGN KEY (item_id) REFERENCES item(id) ON DELETE CASCADE,
    CONSTRAINT fk_itemtag_tag  FOREIGN KEY (tag_id)  REFERENCES tag(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Messages du formulaire de contact
-- ------------------------------------------------------------
CREATE TABLE message (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    sujet      VARCHAR(50)  NOT NULL,
    texte      TEXT NOT NULL,
    lu         TINYINT(1) NOT NULL DEFAULT 0,
    date_envoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Collections personnalisées d'items
-- ------------------------------------------------------------
CREATE TABLE collection (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(150) NOT NULL,
    operator_id   INT UNSIGNED NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_collection_operator FOREIGN KEY (operator_id) REFERENCES operator(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE collection_item (
    collection_id INT UNSIGNED NOT NULL,
    item_id       INT UNSIGNED NOT NULL,
    PRIMARY KEY (collection_id, item_id),
    CONSTRAINT fk_colitem_collection FOREIGN KEY (collection_id) REFERENCES collection(id) ON DELETE CASCADE,
    CONSTRAINT fk_colitem_item       FOREIGN KEY (item_id)       REFERENCES item(id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES
-- ============================================================

-- Admin : admin@bechill.be / admin123
INSERT INTO operator (id, nom, email, mot_de_passe, role, actif) VALUES
(1, 'Administrateur', 'admin@bechill.be', '$2y$10$ZZyNGdxGlwnXaGd1mYYB9OhMxZZimWo8OSOL98Tto20/Moje/vxpi', 'admin', 1);

INSERT INTO category (id, nom) VALUES
(1, 'Massage classique'),
(2, 'Massage spécifique');

INSERT INTO theme (id, nom) VALUES
(1, 'Relaxation'),
(2, 'Récupération sportive'),
(3, 'Bien-être ciblé');

INSERT INTO tag (id, nom) VALUES
(1, 'huiles essentielles'),
(2, 'détente'),
(3, 'anti-stress'),
(4, 'muscles'),
(5, 'circulation'),
(6, 'pierres chaudes'),
(7, 'sport'),
(8, 'récupération'),
(9, 'dos'),
(10, 'nuque'),
(11, 'pieds'),
(12, 'format court');

INSERT INTO item (id, titre, slug, description_courte, description, duree, prix, statut, operator_id, theme_id, category_id) VALUES
(1, 'Massage relaxant', 'massage-relaxant',
 'Un massage doux à l''huile essentielle qui détend l''ensemble du corps.',
 'Le massage relaxant est un soin doux et enveloppant qui vise à détendre l''ensemble du corps. À l''aide de mouvements lents et fluides, votre thérapeute relâche les tensions accumulées et vous aide à retrouver un état de calme profond.\n\nCe soin est idéal pour les personnes stressées, fatiguées ou souhaitant simplement s''offrir un moment de détente. Aucune expérience préalable n''est nécessaire.',
 60, 65.00, 'publie', 1, 1, 1),
(2, 'Massage suédois', 'massage-suedois',
 'Un massage tonique qui alterne pressions profondes et effleurages.',
 'Un massage plus tonique qui alterne pressions profondes et effleurages. Il améliore la circulation sanguine et soulage les tensions musculaires.\n\nPétrissages et frictions, travail en profondeur des muscles, adapté à tous les niveaux de tolérance.',
 60, 70.00, 'publie', 1, 1, 1),
(3, 'Massage suédois long', 'massage-suedois-90',
 'La version 90 minutes du massage suédois, pour un travail complet.',
 'Le massage suédois en version longue : 90 minutes de pétrissages, frictions et effleurages pour un travail en profondeur de l''ensemble des muscles. Il améliore la circulation sanguine et soulage durablement les tensions.',
 90, 95.00, 'publie', 1, 1, 1),
(4, 'Massage aux pierres chaudes', 'massage-pierres-chaudes',
 'Des pierres volcaniques chauffées pour une relaxation profonde.',
 'Des pierres volcaniques chauffées sont placées sur les points de tension du corps. La chaleur pénètre les muscles et procure une relaxation profonde.\n\nPierres de basalte naturelles, chaleur thérapeutique, relâchement musculaire profond.',
 75, 85.00, 'publie', 1, 1, 1),
(5, 'Massage sportif', 'massage-sportif',
 'Un massage ciblé qui favorise la récupération après l''effort.',
 'Conçu pour les sportifs, ce massage cible les zones sollicitées lors de l''effort. Il favorise la récupération et prévient les blessures.\n\nTravail ciblé sur les zones de tension, étirements assistés, adapté avant ou après l''effort.',
 60, 70.00, 'publie', 1, 2, 2),
(6, 'Massage sportif long', 'massage-sportif-90',
 'La version 90 minutes du massage sportif, avec étirements assistés.',
 'Le massage sportif en version longue : 90 minutes de travail ciblé sur les zones sollicitées lors de l''effort, complété par des étirements assistés. Idéal après une compétition ou une période d''entraînement intensif.',
 90, 95.00, 'publie', 1, 2, 2),
(7, 'Massage dos et nuque', 'massage-dos-nuque',
 'Un soin court ciblé sur les épaules, la nuque et le dos.',
 'Un soin ciblé sur le haut du corps pour les personnes souffrant de tensions liées au travail de bureau ou au stress quotidien.\n\nFocus sur les épaules, la nuque et le dos, soulagement rapide des tensions, format court idéal en pause déjeuner.',
 30, 40.00, 'publie', 1, 3, 2),
(8, 'Réflexologie plantaire', 'reflexologie-plantaire',
 'La stimulation des zones réflexes du pied pour un équilibre général.',
 'Un soin basé sur la stimulation des zones réflexes du pied. Il favorise l''équilibre général du corps et procure une détente profonde.\n\nPressions ciblées sur les zones réflexes, amélioration de la circulation, bien-être général.',
 45, 55.00, 'publie', 1, 3, 2);

INSERT INTO item_tag (item_id, tag_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 4), (2, 5),
(3, 4), (3, 5),
(4, 6), (4, 2), (4, 4),
(5, 7), (5, 8), (5, 4),
(6, 7), (6, 8), (6, 4),
(7, 9), (7, 10), (7, 12),
(8, 11), (8, 5), (8, 2);

INSERT INTO message (nom, email, sujet, texte, lu) VALUES
('Marie Dupont', 'marie.dupont@example.com', 'information',
 'Bonjour, proposez-vous des bons cadeaux pour le massage aux pierres chaudes ? Merci d''avance.', 0),
('Karim Benali', 'karim.benali@example.com', 'reservation',
 'Bonjour, est-il possible de déplacer mon rendez-vous de samedi à dimanche ?', 1);
