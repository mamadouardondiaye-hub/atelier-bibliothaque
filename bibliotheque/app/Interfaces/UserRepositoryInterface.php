<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /** @return User[] */
    public function all(): array;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    /** @return User[] */
    public function search(string $term): array;

    public function create(array $data): User;

    public function update(int $id, array $data): void;

    public function updatePassword(int $id, string $password): void;

    public function delete(int $id): void;

    public function count(): int;

    /** @return array{id: int, libelle: string}[] */
    public function roles(): array;

    /** @return User[] */
    public function mostActive(int $limit = 5): array;
}
