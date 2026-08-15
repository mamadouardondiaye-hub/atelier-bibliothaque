# Atelier Bibliothèque — Mamadou Ardo Ndiaye (ESP 221)

Architecture, routes, vues, règles métier : strictement identiques à l'original. Seule la couche
base de données a changé.

## Lancer et tester

Prérequis : PHP 8.1+ avec l'extension `pdo_pgsql`, PostgreSQL actif.

```bash
cd bibliotheque

# 1. Vérifier les identifiants dans config/database.php
#    (par défaut : host=localhost, port=5432, user=postgres, password=postgres)

# 2. Créer la base + les données de démo
php database/seed.php

# 3. Démarrer l'application
cd public && php -S localhost:8000 index.php
# puis http://localhost:8000/login
```

Comptes de démo : `admin@biblio.fr` / `admin123` (Admin), `biblio@biblio.fr` / `biblio123`
(Bibliothécaire), `alice.martin@mail.fr` / `lecteur123` (Lecteur).

## Testé en réel (pas juste relu)

- Connexion par session + protection CSRF (`_token`)
- Contrôle d'accès par rôle (403 pour un Lecteur sur `/users`)
- Recherche insensible à la casse (`ILIKE`) sur titres/auteurs/ISBN
- Emprunt d'un livre (décrémente le stock, respecte la règle des 3 emprunts max, bloque un livre
  à quantité 0 — le formulaire d'emprunt disparaît déjà côté vue dans ce cas)
- Tableau de bord (statistiques agrégées, `GROUP BY` corrigé pour PostgreSQL)
- Création d'une catégorie (Admin)

## Ce qui a changé pour PostgreSQL (rien d'autre)

- `database/schema.sql` : `AUTO_INCREMENT` → `SERIAL`, `ENUM` → `CHECK`, retrait de
  `ENGINE = InnoDB`, `DATETIME` → `TIMESTAMP`, et `CASCADE` sur les `DROP TABLE` (nécessaire car la
  base est partagée avec l'atelier API : si `api_tokens` existe déjà avec sa clé étrangère vers
  `users`, un simple `DROP TABLE users` échoue sans `CASCADE`).
- Plus de `CREATE DATABASE IF NOT EXISTS` / `USE` : `seed.php` se connecte d'abord à la base de
  maintenance `postgres` pour créer la base si besoin.
- `Core/Database.php` : DSN `mysql:...` → `pgsql:host=...;port=...;dbname=...`.
- Dans les repositories (`BorrowRepository`, `UserRepository`), les littéraux `"en_cours"`,
  `"retourne"`, `"Lecteur"` (guillemets doubles) sont devenus `'en_cours'`, `'retourne'`,
  `'Lecteur'` (guillemets simples) — en PostgreSQL les guillemets doubles désignent un
  **identifiant**, pas une chaîne de caractères comme en MySQL.
- `lastInsertId()` reçoit explicitement le nom de la séquence (ex. `'books_id_seq'`).
- `LIKE` → `ILIKE` dans les recherches (livres, utilisateurs), pour garder un comportement
  insensible à la casse équivalent à la collation `utf8mb4_unicode_ci` de MySQL.
- `BookRepository::mostBorrowed()` et `UserRepository::mostActive()` : `GROUP BY` complété
  (`c.libelle`, `r.libelle`) — PostgreSQL exige que toute colonne non agrégée du `SELECT` apparaisse
  dans le `GROUP BY` (sauf dépendance fonctionnelle stricte à la clé primaire de la table groupée),
  contrairement à MySQL qui tolère un `GROUP BY` partiel par défaut.

Aucune autre ligne (routes, contrôleurs, services, vues, règles métier, middleware) n'a été
modifiée par rapport à l'original du prof.
