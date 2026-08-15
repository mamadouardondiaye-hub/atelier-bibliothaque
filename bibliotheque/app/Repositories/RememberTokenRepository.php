<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RememberTokenRepositoryInterface;
use PDO;

class RememberTokenRepository implements RememberTokenRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function create(int $userId, string $tokenHash, string $expiresAt): void
    {
        $this->pdo->prepare(
            'INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
        )->execute([$userId, $tokenHash, $expiresAt]);
    }

    public function findUserIdByHash(string $tokenHash): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT user_id FROM remember_tokens WHERE token_hash = ? AND expires_at > NOW()'
        );
        $stmt->execute([$tokenHash]);

        $userId = $stmt->fetchColumn();

        return $userId === false ? null : (int) $userId;
    }

    public function deleteByHash(string $tokenHash): void
    {
        $this->pdo->prepare('DELETE FROM remember_tokens WHERE token_hash = ?')->execute([$tokenHash]);
    }

    public function deleteByUser(int $userId): void
    {
        $this->pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?')->execute([$userId]);
    }
}
