<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use PDO;

class BookRepository implements BookRepositoryInterface
{
    private const SELECT_WITH_CATEGORY = <<<'SQL'
        SELECT b.*, c.libelle AS categorie_libelle
        FROM books b
        LEFT JOIN categories c ON c.id = b.categorie_id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_CATEGORY . ' ORDER BY b.titre');
        return $this->hydrate($stmt->fetchAll());
    }

    public function search(string $term, ?int $categoryId = null): array
    {
        $sql = self::SELECT_WITH_CATEGORY . ' WHERE (b.titre ILIKE ? OR b.auteur ILIKE ? OR b.isbn ILIKE ?)';
        $params = ['%' . $term . '%', '%' . $term . '%', '%' . $term . '%'];

        if ($categoryId !== null) {
            $sql .= ' AND b.categorie_id = ?';
            $params[] = $categoryId;
        }

        $sql .= ' ORDER BY b.titre';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $this->hydrate($stmt->fetchAll());
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_CATEGORY . ' WHERE b.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Book::fromRow($row);
    }

    public function findAvailable(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_CATEGORY . ' WHERE b.quantite > 0 ORDER BY b.titre');
        return $this->hydrate($stmt->fetchAll());
    }

    public function findUnavailable(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_CATEGORY . ' WHERE b.quantite <= 0 ORDER BY b.titre');
        return $this->hydrate($stmt->fetchAll());
    }

    public function create(array $data): Book
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (isbn, titre, auteur, description, date_publication, quantite, categorie_id, couverture)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['isbn'],
            $data['titre'],
            $data['auteur'],
            $data['description'] ?? '',
            $data['date_publication'] ?? null,
            $data['quantite'],
            $data['categorie_id'] ?? null,
            $data['couverture'] ?? null,
        ]);

        $id = (int) $this->pdo->lastInsertId('books_id_seq');
        return $this->findById($id) ?? throw new \RuntimeException('Livre introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE books SET isbn = ?, titre = ?, auteur = ?, description = ?, date_publication = ?, quantite = ?, categorie_id = ?, couverture = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['isbn'],
            $data['titre'],
            $data['auteur'],
            $data['description'] ?? '',
            $data['date_publication'] ?? null,
            $data['quantite'],
            $data['categorie_id'] ?? null,
            $data['couverture'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM books WHERE id = ?')->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
    }

    public function countAvailable(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM books WHERE quantite > 0')->fetchColumn();
    }

    public function mostBorrowed(int $limit = 5): array
    {
        $stmt = $this->pdo->query(
            'SELECT b.*, c.libelle AS categorie_libelle, COUNT(bo.id) AS total_emprunts
             FROM books b
             LEFT JOIN categories c ON c.id = b.categorie_id
             LEFT JOIN borrows bo ON bo.book_id = b.id
             GROUP BY b.id, c.libelle
             ORDER BY total_emprunts DESC, b.titre
             LIMIT ' . (int) $limit
        );
        return $this->hydrate($stmt->fetchAll());
    }

    public function decrementStock(int $id): void
    {
        $this->pdo->prepare('UPDATE books SET quantite = quantite - 1 WHERE id = ?')->execute([$id]);
    }

    public function incrementStock(int $id): void
    {
        $this->pdo->prepare('UPDATE books SET quantite = quantite + 1 WHERE id = ?')->execute([$id]);
    }

    /** @param array[] $rows */
    private function hydrate(array $rows): array
    {
        return array_map(Book::fromRow(...), $rows);
    }
}
