<?php

declare(strict_types=1);

namespace App\Interfaces;

interface RememberTokenRepositoryInterface
{
    public function create(int $userId, string $tokenHash, string $expiresAt): void;

    /** Retourne l'identifiant de l'utilisateur si le token est encore valide. */
    public function findUserIdByHash(string $tokenHash): ?int;

    public function deleteByHash(string $tokenHash): void;

    public function deleteByUser(int $userId): void;
}
