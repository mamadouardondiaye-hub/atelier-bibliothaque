<?php

declare(strict_types=1);

namespace App\Models;

class Category
{
    public function __construct(
        public readonly int $id,
        public readonly string $libelle,
        public readonly string $description,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            libelle: $row['libelle'],
            description: $row['description'],
        );
    }
}
