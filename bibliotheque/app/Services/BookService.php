<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Exceptions\ValidationException;

class BookService
{
    public function __construct(private BookRepositoryInterface $books) {}

    public function create(array $data): Book
    {
        $this->validate($data);

        return $this->books->create([
            'isbn'              => $data['isbn'],
            'titre'             => $data['titre'],
            'auteur'            => $data['auteur'],
            'description'       => $data['description'] ?? '',
            'date_publication'  => $data['date_publication'] ?? null,
            'quantite'          => max(0, (int) $data['quantite']),
            'categorie_id'      => $data['categorie_id'] ?: null,
            'couverture'        => $data['couverture'] ?? null,
        ]);
    }

    public function update(int $id, array $data): void
    {
        $this->validate($data);

        $this->books->update($id, [
            'isbn'              => $data['isbn'],
            'titre'             => $data['titre'],
            'auteur'            => $data['auteur'],
            'description'       => $data['description'] ?? '',
            'date_publication'  => $data['date_publication'] ?? null,
            'quantite'          => max(0, (int) $data['quantite']),
            'categorie_id'      => $data['categorie_id'] ?: null,
            'couverture'        => $data['couverture'] ?? null,
        ]);
    }

    private function validate(array $data): void
    {
        if (trim($data['isbn']) === '') {
            throw new ValidationException("L'ISBN est obligatoire.");
        }
        if (trim($data['titre']) === '') {
            throw new ValidationException('Le titre est obligatoire.');
        }
        if (trim($data['auteur']) === '') {
            throw new ValidationException("L'auteur est obligatoire.");
        }
    }
}
