<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\BorrowController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;

$router->get('/', [DashboardController::class, 'index'], ['auth']);

$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->get('/logout', [AuthController::class, 'logout'], ['auth']);

$router->get('/users', [UserController::class, 'index'], ['auth', 'role:Admin']);
$router->get('/users/create', [UserController::class, 'create'], ['auth', 'role:Admin']);
$router->post('/users', [UserController::class, 'store'], ['auth', 'role:Admin']);
$router->get('/users/{id}/edit', [UserController::class, 'edit'], ['auth', 'role:Admin']);
$router->post('/users/{id}/update', [UserController::class, 'update'], ['auth', 'role:Admin']);
$router->post('/users/{id}/delete', [UserController::class, 'delete'], ['auth', 'role:Admin']);

$router->get('/categories', [CategoryController::class, 'index'], ['auth', 'role:Admin,Bibliothecaire']);
$router->get('/categories/create', [CategoryController::class, 'create'], ['auth', 'role:Admin,Bibliothecaire']);
$router->post('/categories', [CategoryController::class, 'store'], ['auth', 'role:Admin,Bibliothecaire']);
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit'], ['auth', 'role:Admin,Bibliothecaire']);
$router->post('/categories/{id}/update', [CategoryController::class, 'update'], ['auth', 'role:Admin,Bibliothecaire']);
$router->post('/categories/{id}/delete', [CategoryController::class, 'delete'], ['auth', 'role:Admin']);

$router->get('/books', [BookController::class, 'index'], ['auth']);
$router->get('/books/create', [BookController::class, 'create'], ['auth', 'role:Admin,Bibliothecaire']);
$router->post('/books', [BookController::class, 'store'], ['auth', 'role:Admin,Bibliothecaire']);
$router->get('/books/{id}', [BookController::class, 'show'], ['auth']);
$router->get('/books/{id}/edit', [BookController::class, 'edit'], ['auth', 'role:Admin,Bibliothecaire']);
$router->post('/books/{id}/update', [BookController::class, 'update'], ['auth', 'role:Admin,Bibliothecaire']);
$router->post('/books/{id}/delete', [BookController::class, 'delete'], ['auth', 'role:Admin']);

$router->post('/books/{id}/borrow', [BorrowController::class, 'store'], ['auth']);

$router->get('/borrows', [BorrowController::class, 'index'], ['auth', 'role:Admin,Bibliothecaire']);
$router->get('/my-borrows', [BorrowController::class, 'my'], ['auth']);
$router->post('/borrows/{id}/return', [BorrowController::class, 'update'], ['auth']);
