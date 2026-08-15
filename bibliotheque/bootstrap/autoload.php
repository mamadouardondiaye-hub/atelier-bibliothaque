<?php

declare(strict_types=1);

spl_autoload_register(function (string $classe): void {
    $prefixes = [
        'App\\'       => __DIR__ . '/../app/',
        'Core\\'      => __DIR__ . '/../Core/',
        'Exceptions\\' => __DIR__ . '/../Exceptions/',
    ];

    foreach ($prefixes as $prefixe => $base) {
        if (str_starts_with($classe, $prefixe)) {
            $chemin = $base . str_replace('\\', '/', substr($classe, strlen($prefixe))) . '.php';
            if (file_exists($chemin)) {
                require $chemin;
                return;
            }
        }
    }
});

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', __DIR__ . '/../views');
}

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('csrf')) {
    function csrf(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }
}

if (!function_exists('image_url')) {
    /**
     * Transforme le chemin stocké en base (ex. /uploads/x.png) en URL complète.
     * Les URLs absolues (Cloudinary) sont renvoyées telles quelles.
     */
    function image_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return \Core\View::baseUrl() . '/' . ltrim($path, '/');
    }
}
