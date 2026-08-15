<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\BorrowRepositoryInterface;
use App\Models\Borrow;
use Exceptions\BusinessException;
use Exceptions\NotFoundException;

class BorrowService
{
    public const MAX_EMPRUNTS = 3;
    public const DUREE_JOURS = 21;

    public function __construct(
        private BorrowRepositoryInterface $borrows,
        private BookRepositoryInterface $books,
    ) {}

    public function borrow(int $userId, int $bookId): Borrow
    {
        $book = $this->books->findById($bookId);

        if ($book === null) {
            throw new NotFoundException('Livre introuvable.');
        }
        if (!$book->disponible()) {
            throw new BusinessException("Ce livre n'est pas disponible.");
        }
        if ($this->borrows->countActiveByUser($userId) >= self::MAX_EMPRUNTS) {
            throw new BusinessException(sprintf(
                'Vous ne pouvez pas emprunter plus de %d livres simultanément.',
                self::MAX_EMPRUNTS
            ));
        }
        if ($this->borrows->hasActiveForUserAndBook($userId, $bookId)) {
            throw new BusinessException('Vous avez déjà emprunté ce livre.');
        }

        $this->books->decrementStock($bookId);

        return $this->borrows->create($userId, $bookId);
    }

    public function return(int $borrowId): void
    {
        $borrow = $this->borrows->findById($borrowId);

        if ($borrow === null || !$borrow->estEnCours()) {
            throw new NotFoundException('Emprunt introuvable ou déjà retourné.');
        }

        $this->borrows->markReturned($borrowId);
        $this->books->incrementStock($borrow->bookId);
    }
}
