<?php

declare(strict_types=1);

namespace Core;

use PDO;

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $config = require dirname(__DIR__) . '/config/database.php';

            self::$pdo = new PDO(
                sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s',
                    $config['host'],
                    $config['port'],
                    $config['dbname']
                ),
                $config['user'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }

        return self::$pdo;
    }
}
