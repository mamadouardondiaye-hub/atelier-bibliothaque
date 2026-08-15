# Bibliothèque du Savoir — Projet Fil Rouge

Application web de gestion d'une bibliothèque numérique, construite progressivement tout au long du cours de **PHP orienté objet**.
Ce projet réunit l'ensemble des notions vues dans les 18 ateliers pour aboutir à une **architecture en couches inspirée de Laravel**.

## Fonctionnalités

| Module | Fonctionnalités |
|---|---|
| **Authentification** | connexion / déconnexion, sessions, protection des routes (middleware), **« se souvenir de moi »** (token + cookie) |
| **Rôles** | Admin, Bibliothécaire, Lecteur |
| **Utilisateurs (Admin)** | lister, créer, modifier, supprimer, rechercher, changer le rôle |
| **Catégories (Admin / Biblio)** | lister, créer, modifier, supprimer |
| **Livres (Admin / Biblio)** | lister, rechercher, filtrer par catégorie / disponibilité, ajouter, modifier, supprimer, **couverture (upload local ou Cloudinary)** |
| **Emprunts** | emprunter, retourner, historique par lecteur, gestion des retards |
| **Tableau de bord** | statistiques : total livres, livres disponibles, utilisateurs, emprunts en cours / retournés, livres les plus empruntés, lecteurs les plus actifs |

### Sécurité & mots de passe

- les mots de passe sont **hachés** avec `password_hash()` (bcrypt) et vérifiés avec `password_verify()` — jamais stockés en clair ;
- le **« se souvenir de moi »** repose sur un token aléatoire (64 caractères hexadécimaux) stocké dans un cookie `HttpOnly` + `SameSite` ; seule son **empreinte SHA-256** est conservée en base (si la base est compromise, le cookie est inutilisable) ; le token expire après 30 jours et est révoqué à la déconnexion ;
- upload d'images contrôlé : type MIME vérifié (finfo), taille limitée (2 Mo).

### Règles métier

- un lecteur ne peut pas emprunter plus de **3 livres** en même temps ;
- un livre **indisponible** (quantité à 0) ne peut pas être emprunté ;
- un lecteur ne peut pas emprunter **le même livre deux fois** simultanément ;
- lors d'un emprunt, le **stock diminue automatiquement** ;
- lors d'un retour, le **stock augmente automatiquement** ;
- la date de retour prévue est fixée à **+21 jours**.

## Comptes de démonstration

| Rôle | E-mail | Mot de passe |
|---|---|---|
| Administrateur | `admin@biblio.fr` | `admin123` |
| Bibliothécaire | `biblio@biblio.fr` | `biblio123` |
| Lecteur | `alice.martin@mail.fr` | `lecteur123` |

## Installation

### 1. Prérequis

- PHP 8.1+ (extension PDO MySQL activée)
- MySQL / MariaDB (XAMPP, WAMP ou Laragon)
- Apache avec `mod_rewrite` (actif par défaut dans XAMPP)

### 2. Copier le projet

Copier le dossier `bibliotheque/` dans le répertoire web de votre serveur :
`htdocs/` (XAMPP), `www/` (WAMP) ou `www/` (Laragon).

### 3. Configurer la base de données

Éditer `config/database.php` et renseigner vos identifiants MySQL.

### 4. Créer les tables et les données

Depuis le dossier `bibliotheque/` :

```bash
php database/seed.php
```

Ce script exécute `database/schema.sql` (création des tables) puis insère les rôles, utilisateurs, catégories, livres et emprunts de démonstration.

### 5. (Optionnel) Activer Cloudinary

Pour stocker les couvertures sur **Cloudinary** (au lieu du disque local) :

1. Créez un compte gratuit sur <https://cloudinary.com/> ;
2. Renseignez vos identifiants (`cloud_name`, `api_key`, `api_secret`) dans `config/cloudinary.php`.

Sans identifiants, les images sont enregistrées localement dans `public/uploads/` : l'application fonctionne de la même façon.

### 6. Lancer l'application

Avec Apache : ouvrir <http://localhost/bibliotheque/public/>

Sans Apache (serveur de développement PHP) :

```bash
php -S localhost:8000 -t public public/index.php
```

> Le second argument (`public/index.php`) est le **routeur** : il permet au serveur de développement de renvoyer
> toutes les requêtes (ex. `/books`, `/login`) vers le front controller, comme le ferait Apache avec `.htaccess`.

Puis ouvrir <http://localhost:8000/>.

## Architecture

```
bibliotheque/
├── app/
│   ├── Controllers/       Contrôleurs (récupèrent la requête, appellent les services)
│   ├── Models/            Modèles métier (User, Category, Book, Borrow)
│   ├── Repositories/      Accès aux données (SQL), programmés contre des interfaces
│   ├── Services/          Logique métier (auth, emprunts, livres, statistiques, upload)
│   ├── Interfaces/        Contrats des repositories (dépendance d'abstraction)
│   └── Middleware/        Contrôle d'accès (auth, guest, rôle)
├── Core/                  Le mini-framework : Router, Container, Database, View
├── Exceptions/            Hiérarchie d'exceptions applicatives
├── config/                Configuration (base de données, Cloudinary)
├── database/              Schéma SQL + script de données (seed)
├── public/                Point d'entrée unique (front controller) + doss. uploads/
├── routes/                Déclaration des routes
├── views/                 Vues (Tailwind CSS via CDN)
└── bootstrap/             Autoloading (PSR-4 simplifié) + helpers
```

## Concepts mis en œuvre

- **POO** : classes, objets, encapsulation, héritage, polymorphisme, classes abstraites, interfaces
- **Namespaces & autoloading** : préfixes `App\`, `Core\`, `Exceptions\`
- **Architecture en couches** : Controllers → Services → Repositories → Modèles
- **Injection de dépendances & Container IoC** : résolution automatique par réflexion
- **Exceptions** : hiérarchie applicative gérée de façon centralisée par le Router
- **Authentification & rôles** : sessions, `password_hash` / `password_verify`, middleware de routes
- **Tokens & « se souvenir de moi »** : `random_bytes`, stockage haché (SHA-256), cookie sécurisé, révocation à la déconnexion
- **Upload de fichiers** : `$_FILES`, `move_uploaded_file`, validation MIME (finfo), envoi multipart vers **Cloudinary** (cURL, signature SHA-1)
- **Sécurité** : requêtes préparées (PDO), protection CSRF, échappement des sorties (`htmlspecialchars`)

## Parcourir le code (bon point de départ)

1. `public/index.php` — le point d'entrée : autoload, session, container, router, reconnexion « se souvenir de moi ».
2. `routes/web.php` — toutes les routes avec leurs middlewares.
3. `Core/Router.php` — correspondance URL → contrôleur, middleware, gestion des exceptions.
4. `app/Services/BorrowService.php` — les règles métier des emprunts.
5. `app/Services/UploadService.php` — l'upload d'images (local ou Cloudinary).
6. `app/Services/AuthService.php` — connexion, hachage des mots de passe et token « se souvenir de moi ».
7. `app/Repositories/BookRepository.php` — un exemple de repository SQL.
