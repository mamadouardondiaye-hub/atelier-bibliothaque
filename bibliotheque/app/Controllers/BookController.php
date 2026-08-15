<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Services\BookService;
use App\Services\UploadService;
use Core\View;
use Exceptions\ValidationException;

class BookController extends Controller
{
    public function __construct(
        private BookRepositoryInterface $books,
        private CategoryRepositoryInterface $categories,
        private BookService $bookService,
        private UploadService $uploads,
    ) {}

    public function index(): string
    {
        $term = trim((string) $this->value('q', ''));
        $categoryId = $this->value('categorie');
        $filter = (string) $this->value('dispo', 'all');

        $categoryId = ($categoryId !== null && $categoryId !== '') ? (int) $categoryId : null;

        if ($term !== '' || $categoryId !== null) {
            $books = $this->books->search($term, $categoryId);
        } elseif ($filter === 'disponible') {
            $books = $this->books->findAvailable();
        } elseif ($filter === 'indisponible') {
            $books = $this->books->findUnavailable();
        } else {
            $books = $this->books->all();
        }

        return View::render('books/index', [
            'title'      => 'Catalogue des livres',
            'books'      => $books,
            'categories' => $this->categories->all(),
            'term'       => $term,
            'categoryId' => $categoryId,
            'filter'     => $filter,
        ]);
    }

    public function show(int $id): string
    {
        $book = $this->books->findById($id);

        if ($book === null) {
            http_response_code(404);
            return View::render('errors/404', ['title' => 'Livre introuvable'], null);
        }

        return View::render('books/show', [
            'title' => $book->titre,
            'book'  => $book,
        ]);
    }

    public function create(): string
    {
        return View::render('books/create', [
            'title'      => 'Nouveau livre',
            'categories' => $this->categories->all(),
        ]);
    }

    public function store(): never
    {
        try {
            $data = $this->bookInput();
            $data['couverture'] = $this->uploads->upload($_FILES['couverture'] ?? []);
            $this->bookService->create($data);
            flash('success', 'Livre ajouté au catalogue.');
            View::redirect('/books');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/books/create');
        }
    }

    public function edit(int $id): string
    {
        $book = $this->books->findById($id);

        if ($book === null) {
            http_response_code(404);
            return View::render('errors/404', ['title' => 'Livre introuvable'], null);
        }

        return View::render('books/edit', [
            'title'      => 'Modifier un livre',
            'book'       => $book,
            'categories' => $this->categories->all(),
        ]);
    }

    public function update(int $id): never
    {
        try {
            $data = $this->bookInput();

            $couverture = $this->uploads->upload($_FILES['couverture'] ?? []);
            if ($couverture !== null) {
                $data['couverture'] = $couverture;
            }

            $this->bookService->update($id, $data);
            flash('success', 'Livre modifié avec succès.');
            View::redirect('/books');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/books/' . $id . '/edit');
        }
    }

    public function delete(int $id): never
    {
        $this->books->delete($id);
        flash('success', 'Livre supprimé.');
        View::redirect('/books');
    }

    /** @return array<string, mixed> */
    private function bookInput(): array
    {
        return [
            'isbn'             => trim((string) $this->value('isbn')),
            'titre'            => trim((string) $this->value('titre')),
            'auteur'           => trim((string) $this->value('auteur')),
            'description'      => trim((string) $this->value('description', '')),
            'date_publication' => $this->value('date_publication') ?: null,
            'quantite'         => (int) $this->value('quantite', 0),
            'categorie_id'     => (int) ($this->value('categorie_id') ?: 0),
        ];
    }
}
