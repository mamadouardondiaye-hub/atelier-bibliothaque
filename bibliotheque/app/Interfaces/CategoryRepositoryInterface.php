<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function all(): array;

    public function findById(int $id): ?Category;

    public function create(array $data): Category;

    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    public function count(): int;
}
