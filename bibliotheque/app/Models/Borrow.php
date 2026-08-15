<?php

declare(strict_types=1);

namespace App\Models;

class Borrow
{
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_RETOURNE = 'retourne';

    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly int $bookId,
        public readonly string $dateEmprunt,
        public readonly string $dateRetourPrevue,
        public readonly ?string $dateRetour,
        public readonly string $statut,
        public readonly ?string $userNom = null,
        public readonly ?string $userPrenom = null,
        public readonly ?string $bookTitre = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            bookId: (int) $row['book_id'],
            dateEmprunt: $row['date_emprunt'],
            dateRetourPrevue: $row['date_retour_prevue'],
            dateRetour: $row['date_retour'],
            statut: $row['statut'],
            userNom: $row['user_nom'] ?? null,
            userPrenom: $row['user_prenom'] ?? null,
            bookTitre: $row['book_titre'] ?? null,
        );
    }

    public function estEnCours(): bool
    {
        return $this->statut === self::STATUT_EN_COURS;
    }

    public function estEnRetard(): bool
    {
        return $this->estEnCours() && $this->dateRetourPrevue < date('Y-m-d');
    }
}
