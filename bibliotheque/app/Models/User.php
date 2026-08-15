<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $telephone,
        public readonly int $roleId,
        public readonly string $role,
        public readonly string $dateCreation,
        public readonly int $totalEmprunts = 0,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            nom: $row['nom'],
            prenom: $row['prenom'],
            email: $row['email'],
            password: $row['password'],
            telephone: $row['telephone'] ?? null,
            roleId: (int) $row['role_id'],
            role: $row['role_libelle'],
            dateCreation: $row['date_creation'],
            totalEmprunts: (int) ($row['total_emprunts'] ?? 0),
        );
    }

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
