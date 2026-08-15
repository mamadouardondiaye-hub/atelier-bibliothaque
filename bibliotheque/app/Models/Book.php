<?php

declare(strict_types=1);

namespace App\Models;

class Book
{
    public function __construct(
        public readonly int $id,
        public readonly string $isbn,
        public readonly string $titre,
        public readonly string $auteur,
        public readonly string $description,
        public readonly ?string $datePublication,
        public readonly int $quantite,
        public readonly ?int $categorieId,
        public readonly ?string $categorieLibelle,
        public readonly ?string $couverture = null,
        public readonly int $totalEmprunts = 0,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            isbn: $row['isbn'],
            titre: $row['titre'],
            auteur: $row['auteur'],
            description: $row['description'],
            datePublication: $row['date_publication'] ?? null,
            quantite: (int) $row['quantite'],
            categorieId: $row['categorie_id'] !== null ? (int) $row['categorie_id'] : null,
            categorieLibelle: $row['categorie_libelle'] ?? null,
            couverture: $row['couverture'] ?? null,
            totalEmprunts: (int) ($row['total_emprunts'] ?? 0),
        );
    }

    public function disponible(): bool
    {
        return $this->quantite > 0;
    }

    public function statut(): string
    {
        return $this->disponible() ? 'Disponible' : 'Indisponible';
    }
}
