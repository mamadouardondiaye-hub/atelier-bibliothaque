<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Borrow;

interface BorrowRepositoryInterface
{
    /** @return Borrow[] */
    public function all(): array;

    /** @return Borrow[] */
    public function current(): array;

    public function findById(int $id): ?Borrow;

    public function create(int $userId, int $bookId): Borrow;

    public function markReturned(int $id): void;

    public function countActive(): int;

    public function countReturned(): int;

    public function countActiveByUser(int $userId): int;

    public function hasActiveForUserAndBook(int $userId, int $bookId): bool;

    /** @return Borrow[] */
    public function byUser(int $userId): array;
}
