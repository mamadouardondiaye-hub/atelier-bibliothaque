<?php

declare(strict_types=1);

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\BorrowRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\RememberTokenRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\RememberTokenRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use Core\Container;
use Core\Database;
use Core\Router;
use Core\View;

session_start();

require __DIR__ . '/../bootstrap/autoload.php';

$container = new Container();

$container->bind(UserRepositoryInterface::class, fn () => new UserRepository(Database::connect()));
$container->bind(CategoryRepositoryInterface::class, fn () => new CategoryRepository(Database::connect()));
$container->bind(BookRepositoryInterface::class, fn () => new BookRepository(Database::connect()));
$container->bind(BorrowRepositoryInterface::class, fn () => new BorrowRepository(Database::connect()));
$container->bind(RememberTokenRepositoryInterface::class, fn () => new RememberTokenRepository(Database::connect()));

if (empty($_SESSION['user'])) {
    $container->make(AuthService::class)->loginFromRememberToken();
}

$router = new Router($container);
require __DIR__ . '/../routes/web.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = View::baseUrl();

if ($base !== '' && str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
}
if ($path === '') {
    $path = '/';
}

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
} catch (\Throwable $e) {
    http_response_code(500);
    echo View::render('errors/500', [
        'title'   => 'Erreur serveur',
        'message' => $e->getMessage(),
    ], null);
}
