<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\BorrowRepositoryInterface;
use App\Models\Borrow;
use PDO;

class BorrowRepository implements BorrowRepositoryInterface
{
    private const SELECT_WITH_JOINS = <<<'SQL'
        SELECT bo.*,
               u.nom AS user_nom, u.prenom AS user_prenom,
               b.titre AS book_titre
        FROM borrows bo
        JOIN users u ON u.id = bo.user_id
        JOIN books b ON b.id = bo.book_id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_JOINS . ' ORDER BY bo.date_emprunt DESC');
        return $this->hydrate($stmt->fetchAll());
    }

    public function current(): array
    {
        $stmt = $this->pdo->query(
            self::SELECT_WITH_JOINS . " WHERE bo.statut = 'en_cours' ORDER BY bo.date_emprunt DESC"
        );
        return $this->hydrate($stmt->fetchAll());
    }

    public function findById(int $id): ?Borrow
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_JOINS . ' WHERE bo.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Borrow::fromRow($row);
    }

    public function create(int $userId, int $bookId): Borrow
    {
        $dateEmprunt = date('Y-m-d');
        $dateRetourPrevue = date('Y-m-d', strtotime('+21 days'));

        $stmt = $this->pdo->prepare(
            "INSERT INTO borrows (user_id, book_id, date_emprunt, date_retour_prevue, statut)
             VALUES (?, ?, ?, ?, 'en_cours')"
        );
        $stmt->execute([$userId, $bookId, $dateEmprunt, $dateRetourPrevue]);

        $id = (int) $this->pdo->lastInsertId('borrows_id_seq');
        return $this->findById($id) ?? throw new \RuntimeException('Emprunt introuvable après création');
    }

    public function markReturned(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE borrows SET date_retour = ?, statut = 'retourne' WHERE id = ?"
        );
        $stmt->execute([date('Y-m-d'), $id]);
    }

    public function countActive(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM borrows WHERE statut = 'en_cours'")->fetchColumn();
    }

    public function countReturned(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM borrows WHERE statut = 'retourne'")->fetchColumn();
    }

    public function countActiveByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM borrows WHERE user_id = ? AND statut = 'en_cours'");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function hasActiveForUserAndBook(int $userId, int $bookId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM borrows WHERE user_id = ? AND book_id = ? AND statut = 'en_cours'"
        );
        $stmt->execute([$userId, $bookId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            self::SELECT_WITH_JOINS . ' WHERE bo.user_id = ? ORDER BY bo.date_emprunt DESC'
        );
        $stmt->execute([$userId]);
        return $this->hydrate($stmt->fetchAll());
    }

    /** @param array[] $rows */
    private function hydrate(array $rows): array
    {
        return array_map(Borrow::fromRow(...), $rows);
    }
}
