<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\BorrowRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;

class StatisticsService
{
    public function __construct(
        private BookRepositoryInterface $books,
        private UserRepositoryInterface $users,
        private BorrowRepositoryInterface $borrows,
    ) {}

    /** @return array<string, int|array> */
    public function dashboard(): array
    {
        return [
            'total_livres'       => $this->books->count(),
            'livres_disponibles' => $this->books->countAvailable(),
            'total_utilisateurs' => $this->users->count(),
            'emprunts_en_cours'  => $this->borrows->countActive(),
            'emprunts_retournes' => $this->borrows->countReturned(),
            'livres_plus_empruntes' => $this->books->mostBorrowed(5),
            'lecteurs_plus_actifs'  => $this->users->mostActive(5),
        ];
    }
}
