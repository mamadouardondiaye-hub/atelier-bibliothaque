-- =============================================================
-- Bibliothèque du Savoir - Schéma de la base de données
-- Adapté pour PostgreSQL (équivalent fonctionnel du schema.sql MySQL du prof)
-- =============================================================

-- La création/sélection de la base se fait depuis seed.php (PostgreSQL n'a pas
-- de CREATE DATABASE IF NOT EXISTS ni de USE : on se connecte directement à la
-- bonne base via le DSN PDO).

-- -------------------------------------------------------------
-- Réinitialisation (rend le script relançable à volonté)
-- Ordre : tables enfants avant les tables parentes (pas de SET FOREIGN_KEY_CHECKS en PostgreSQL)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS remember_tokens CASCADE;
DROP TABLE IF EXISTS borrows CASCADE;
DROP TABLE IF EXISTS books CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

-- -------------------------------------------------------------
-- Rôles
-- -------------------------------------------------------------
CREATE TABLE roles (
    id      SERIAL PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

-- -------------------------------------------------------------
-- Utilisateurs
-- -------------------------------------------------------------
CREATE TABLE users (
    id            SERIAL PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,
    telephone     VARCHAR(30)  NULL,
    role_id       INTEGER NOT NULL REFERENCES roles (id),
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------------
-- Catégories
-- -------------------------------------------------------------
CREATE TABLE categories (
    id          SERIAL PRIMARY KEY,
    libelle     VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL
);

-- -------------------------------------------------------------
-- Livres
-- -------------------------------------------------------------
CREATE TABLE books (
    id               SERIAL PRIMARY KEY,
    isbn             VARCHAR(20) NOT NULL UNIQUE,
    titre            VARCHAR(255) NOT NULL,
    auteur           VARCHAR(255) NOT NULL,
    description      TEXT NULL,
    date_publication DATE NULL,
    quantite         INTEGER NOT NULL DEFAULT 1,
    couverture       VARCHAR(255) NULL,
    categorie_id     INTEGER NULL REFERENCES categories (id) ON DELETE SET NULL
);

-- -------------------------------------------------------------
-- Emprunts
-- Le ENUM MySQL devient un CHECK ici (un vrai type ENUM PostgreSQL existe
-- mais demande une commande à part, un CHECK reste plus simple à faire
-- évoluer dans un seul fichier).
-- -------------------------------------------------------------
CREATE TABLE borrows (
    id                  SERIAL PRIMARY KEY,
    user_id             INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    book_id             INTEGER NOT NULL REFERENCES books (id) ON DELETE CASCADE,
    date_emprunt        DATE NOT NULL,
    date_retour_prevue  DATE NOT NULL,
    date_retour         DATE NULL,
    statut              VARCHAR(20) NOT NULL DEFAULT 'en_cours'
                         CHECK (statut IN ('en_cours', 'retourne'))
);

-- -------------------------------------------------------------
-- Tokens de connexion persistante (« se souvenir de moi »)
-- -------------------------------------------------------------
CREATE TABLE remember_tokens (
    id         SERIAL PRIMARY KEY,
    user_id    INTEGER NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    token_hash CHAR(64) NOT NULL,
    expires_at TIMESTAMP NOT NULL
);
