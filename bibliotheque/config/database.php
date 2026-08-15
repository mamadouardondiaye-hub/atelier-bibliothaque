<?php

declare(strict_types=1);

// Adapte 'user' et 'password' à ton installation PostgreSQL locale
// (par défaut sous Linux : user postgres, mot de passe défini à l'install).
return [
    'host'     => 'localhost',
    'port'     => 5432,
    'dbname'   => 'bibliotheque',
    'user'     => 'postgres',
    'password' => 'postgres',
];
