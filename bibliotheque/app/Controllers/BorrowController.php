<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\BorrowRepositoryInterface;
use App\Services\AuthService;
use App\Services\BorrowService;
use Core\View;

class BorrowController extends Controller
{
    public function __construct(
        private BorrowService $borrowService,
        private BorrowRepositoryInterface $borrows,
        private AuthService $auth,
    ) {}

    public function index(): string
    {
        return View::render('borrows/index', [
            'title'   => 'Gestion des emprunts',
            'borrows' => $this->borrows->current(),
        ]);
    }

    public function my(): string
    {
        $userId = (int) $this->auth->id();

        return View::render('borrows/my', [
            'title'   => 'Mes emprunts',
            'borrows' => $this->borrows->byUser($userId),
        ]);
    }

    public function store(int $bookId): never
    {
        try {
            $userId = (int) $this->auth->id();
            $this->borrowService->borrow($userId, $bookId);
            flash('success', 'Livre emprunté avec succès.');
        } catch (\Exceptions\AppException $e) {
            flash('error', $e->getMessage());
        }

        View::redirect('/books/' . $bookId);
    }

    public function update(int $borrowId): never
    {
        try {
            $this->borrowService->return($borrowId);
            flash('success', 'Livre retourné avec succès.');
        } catch (\Exceptions\AppException $e) {
            flash('error', $e->getMessage());
        }

        View::redirect('/borrows');
    }
}
