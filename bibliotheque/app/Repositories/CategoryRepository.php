<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use PDO;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY libelle');
        return array_map(Category::fromRow(...), $stmt->fetchAll());
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Category::fromRow($row);
    }

    public function create(array $data): Category
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (libelle, description) VALUES (?, ?)');
        $stmt->execute([$data['libelle'], $data['description'] ?? '']);

        $id = (int) $this->pdo->lastInsertId('categories_id_seq');
        return $this->findById($id) ?? throw new \RuntimeException('Catégorie introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET libelle = ?, description = ? WHERE id = ?');
        $stmt->execute([$data['libelle'], $data['description'] ?? '', $id]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    }
}
