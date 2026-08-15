<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Book;

interface BookRepositoryInterface
{
    /** @return Book[] */
    public function all(): array;

    /** @return Book[] */
    public function search(string $term, ?int $categoryId = null): array;

    public function findById(int $id): ?Book;

    /** @return Book[] */
    public function findAvailable(): array;

    /** @return Book[] */
    public function findUnavailable(): array;

    public function create(array $data): Book;

    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    public function count(): int;

    public function countAvailable(): int;

    /** @return Book[] */
    public function mostBorrowed(int $limit = 5): array;

    public function decrementStock(int $id): void;

    public function incrementStock(int $id): void;
}
