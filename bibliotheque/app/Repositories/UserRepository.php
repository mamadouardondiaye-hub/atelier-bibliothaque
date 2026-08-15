<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private const SELECT_WITH_ROLE = <<<'SQL'
        SELECT u.*, r.libelle AS role_libelle
        FROM users u
        JOIN roles r ON r.id = u.role_id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query(
            self::SELECT_WITH_ROLE . ' ORDER BY u.nom, u.prenom'
        );
        return $this->hydrate($stmt->fetchAll());
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : User::fromRow($row);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row === false ? null : User::fromRow($row);
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->pdo->prepare(
            self::SELECT_WITH_ROLE . ' WHERE u.nom ILIKE ? OR u.prenom ILIKE ? OR u.email ILIKE ? ORDER BY u.nom, u.prenom'
        );
        $stmt->execute([$like, $like, $like]);
        return $this->hydrate($stmt->fetchAll());
    }

    public function create(array $data): User
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password, telephone, role_id) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['password'],
            $data['telephone'] ?? null,
            $data['role_id'],
        ]);

        $id = (int) $this->pdo->lastInsertId('users_id_seq');
        return $this->findById($id) ?? throw new \RuntimeException('Utilisateur introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ?, role_id = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'] ?? null,
            $data['role_id'],
            $id,
        ]);
    }

    public function updatePassword(int $id, string $password): void
    {
        $this->pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function mostActive(int $limit = 5): array
    {
        $stmt = $this->pdo->query(
            'SELECT u.*, r.libelle AS role_libelle, COUNT(b.id) AS total_emprunts
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN borrows b ON b.user_id = u.id AND b.statut = \'en_cours\'
             WHERE r.libelle = \'Lecteur\'
             GROUP BY u.id, r.libelle
             ORDER BY total_emprunts DESC, u.nom
             LIMIT ' . (int) $limit
        );
        return $this->hydrate($stmt->fetchAll());
    }

    /** @return array{id: int, libelle: string}[] */
    public function roles(): array
    {
        return $this->pdo->query('SELECT id, libelle FROM roles ORDER BY id')->fetchAll();
    }

    /** @param array[] $rows */
    private function hydrate(array $rows): array
    {
        return array_map(User::fromRow(...), $rows);
    }
}
