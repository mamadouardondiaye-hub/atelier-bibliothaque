<?php

declare(strict_types=1);

/**
 * Script d'initialisation : crée la base + les tables puis insère les données de démonstration.
 * Adapté pour PostgreSQL (le prof avait fait la version MySQL ; la logique et les données
 * insérées sont strictement identiques, seule la mécanique SQL change).
 *
 * Usage :  php database/seed.php
 *          (depuis le dossier bibliotheque/)
 */

$config = require __DIR__ . '/../config/database.php';

// 1. Connexion au serveur PostgreSQL sur la base de maintenance 'postgres'
//    (contrairement à MySQL, PostgreSQL ne permet pas de CREATE DATABASE
//    sans être déjà connecté à une base existante, ni de changer de base
//    en cours de connexion avec un USE).
$pdoMaintenance = new PDO(
    sprintf('pgsql:host=%s;port=%s;dbname=postgres', $config['host'], $config['port']),
    $config['user'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// 2. Création de la base si elle n'existe pas
$stmt = $pdoMaintenance->prepare('SELECT 1 FROM pg_database WHERE datname = ?');
$stmt->execute([$config['dbname']]);
if ($stmt->fetchColumn() === false) {
    $pdoMaintenance->exec(sprintf('CREATE DATABASE "%s"', $config['dbname']));
}
echo "Base de données prête.\n";

// 3. Connexion à la base cible, création des tables (exécution de schema.sql)
$pdo = new PDO(
    sprintf('pgsql:host=%s;port=%s;dbname=%s', $config['host'], $config['port'], $config['dbname']),
    $config['user'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "Création des tables...\n";
$schema = file_get_contents(__DIR__ . '/schema.sql');
foreach (array_filter(array_map('trim', explode(';', $schema))) as $statement) {
    $pdo->exec($statement);
}
echo "Tables créées.\n";

// Note : schema.sql fait un DROP TABLE puis CREATE TABLE à chaque exécution,
// les tables sont donc déjà vides avec des séquences (SERIAL) repartant de 1 —
// pas besoin d'un DELETE + reset d'auto-incrément séparé comme en MySQL.

// 4. Rôles
$insertRole = $pdo->prepare('INSERT INTO roles (libelle) VALUES (?)');
$roleIds = [];
foreach (['Admin', 'Bibliothecaire', 'Lecteur'] as $role) {
    $insertRole->execute([$role]);
    $roleIds[$role] = (int) $pdo->lastInsertId('roles_id_seq');
}
echo "Rôles insérés.\n";

// 5. Utilisateurs
$insertUser = $pdo->prepare(
    'INSERT INTO users (nom, prenom, email, password, telephone, role_id) VALUES (?, ?, ?, ?, ?, ?)'
);

$users = [
    ['Dupont', 'Jean',   'admin@biblio.fr',        'admin123',  '06 11 22 33 44', 'Admin'],
    ['Lambert', 'Marie', 'biblio@biblio.fr',       'biblio123', '06 22 33 44 55', 'Bibliothecaire'],
    ['Martin', 'Alice',  'alice.martin@mail.fr',   'lecteur123', '06 33 44 55 66', 'Lecteur'],
    ['Bernard', 'Lucas', 'lucas.bernard@mail.fr',  'lecteur123', '06 44 55 66 77', 'Lecteur'],
    ['Dubois', 'Emma',   'emma.dubois@mail.fr',    'lecteur123', '06 55 66 77 88', 'Lecteur'],
];

$userIdByIndex = [];
foreach ($users as $index => [$nom, $prenom, $email, $password, $telephone, $role]) {
    $insertUser->execute([
        $nom,
        $prenom,
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        $telephone,
        $roleIds[$role],
    ]);
    $userIdByIndex[$index] = (int) $pdo->lastInsertId('users_id_seq');
}
echo 'Utilisateurs insérés : ' . count($users) . ".\n";

// 6. Catégories
$insertCategory = $pdo->prepare('INSERT INTO categories (libelle, description) VALUES (?, ?)');
$categories = [
    ['Romans', 'Romans et littérature générale.'],
    ['Science-Fiction', 'Science-fiction et fantasy.'],
    ['Histoire', 'Livres d\'histoire et de société.'],
    ['Jeunesse', 'Livres pour enfants et adolescents.'],
    ['Informatique', 'Développement, programmation et systèmes.'],
];
$categoryIdByLibelle = [];
foreach ($categories as [$libelle, $description]) {
    $insertCategory->execute([$libelle, $description]);
    $categoryIdByLibelle[$libelle] = (int) $pdo->lastInsertId('categories_id_seq');
}
echo 'Catégories insérées : ' . count($categories) . ".\n";

// 7. Livres
$insertBook = $pdo->prepare(
    'INSERT INTO books (isbn, titre, auteur, description, date_publication, quantite, categorie_id)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);

$books = [
    ['978-2070368228', 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Un aviateur rencontre un petit prince venu d\'une autre planète.', '1943-04-06', 4, 'Jeunesse'],
    ['978-2253152563', '1984', 'George Orwell', 'Une dystopie sur la surveillance de masse et la pensée unique.', '1949-06-08', 2, 'Science-Fiction'],
    ['978-2253258364', 'Les Misérables', 'Victor Hugo', 'Le destin de Jean Valjean dans la France du XIXe siècle.', '1862-04-03', 3, 'Romans'],
    ['978-2070612758', 'L\'Étranger', 'Albert Camus', 'Le récit d\'un homme étranger au monde qui l\'entoure.', '1942-05-19', 5, 'Romans'],
    ['978-2266286327', 'Harry Potter à l\'école des sorciers', 'J.K. Rowling', 'Le premier tome des aventures du jeune sorcier.', '1997-06-26', 4, 'Jeunesse'],
    ['978-2290356650', 'Fondation', 'Isaac Asimov', 'La saga fondatrice de la science-fiction moderne.', '1951-01-01', 0, 'Science-Fiction'],
    ['978-2081416813', 'Sapiens : une brève histoire de l\'humanité', 'Yuval Noah Harari', 'Un panorama de l\'histoire de l\'humanité.', '2011-09-01', 3, 'Histoire'],
    ['978-2020323411', 'Vingt mille lieues sous les mers', 'Jules Verne', 'Les aventures du capitaine Nemo à bord du Nautilus.', '1870-01-01', 3, 'Romans'],
    ['978-2807307331', 'Apprendre PHP 8', 'Philippe Martin', 'Un guide pratique pour maîtriser PHP 8 et la POO.', '2021-03-15', 2, 'Informatique'],
    ['978-2070423530', 'La Peste', 'Albert Camus', 'La ville d\'Oran face à une épidémie de peste.', '1947-06-10', 1, 'Romans'],
];

$bookIdByIsbn = [];
foreach ($books as [$isbn, $titre, $auteur, $description, $date, $quantite, $categorie]) {
    $insertBook->execute([
        $isbn,
        $titre,
        $auteur,
        $description,
        $date,
        $quantite,
        $categoryIdByLibelle[$categorie],
    ]);
    $bookIdByIsbn[$isbn] = (int) $pdo->lastInsertId('books_id_seq');
}
echo 'Livres insérés : ' . count($books) . ".\n";

// 8. Emprunts (quelques emprunts en cours et retournés)
$insertBorrow = $pdo->prepare(
    'INSERT INTO borrows (user_id, book_id, date_emprunt, date_retour_prevue, date_retour, statut)
     VALUES (?, ?, ?, ?, ?, ?)'
);

$borrows = [
    // [index utilisateur, ISBN livre, jours depuis l'emprunt, retour effectué (0 = en cours)]
    [2, '978-2253152563', 10, 0],   // Alice emprunte 1984
    [2, '978-2070368228', 5, 0],    // Alice emprunte Le Petit Prince
    [3, '978-2290356650', 2, 0],    // Lucas emprunte Fondation
    [2, '978-2253258364', 30, 10],  // Alice a retourné Les Misérables
    [3, '978-2266286327', 20, 5],   // Lucas a retourné Harry Potter
];

foreach ($borrows as [$userIndex, $isbn, $daysAgo, $returnedDaysAgo]) {
    $dateEmprunt = date('Y-m-d', strtotime("-$daysAgo days"));
    $dateRetourPrevue = date('Y-m-d', strtotime($dateEmprunt . ' +21 days'));
    $dateRetour = $returnedDaysAgo > 0
        ? date('Y-m-d', strtotime("-$returnedDaysAgo days"))
        : null;
    $statut = $dateRetour === null ? 'en_cours' : 'retourne';

    $insertBorrow->execute([
        $userIdByIndex[$userIndex],
        $bookIdByIsbn[$isbn],
        $dateEmprunt,
        $dateRetourPrevue,
        $dateRetour,
        $statut,
    ]);
}
echo 'Emprunts insérés : ' . count($borrows) . ".\n";

echo "\nTerminé !\n";
echo "Comptes de démonstration :\n";
echo "  - Admin        : admin@biblio.fr / admin123\n";
echo "  - Bibliothécaire : biblio@biblio.fr / biblio123\n";
echo "  - Lecteur      : alice.martin@mail.fr / lecteur123\n";
