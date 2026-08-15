<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\CategoryRepositoryInterface;
use Core\View;
use Exceptions\ValidationException;

class CategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $categories) {}

    public function index(): string
    {
        return View::render('categories/index', [
            'title'      => 'Catégories',
            'categories' => $this->categories->all(),
        ]);
    }

    public function create(): string
    {
        return View::render('categories/create', ['title' => 'Nouvelle catégorie']);
    }

    public function store(): never
    {
        try {
            $libelle = trim((string) $this->value('libelle'));
            if ($libelle === '') {
                throw new ValidationException('Le libellé est obligatoire.');
            }

            $this->categories->create([
                'libelle'     => $libelle,
                'description' => trim((string) $this->value('description', '')),
            ]);

            flash('success', 'Catégorie créée avec succès.');
            View::redirect('/categories');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/categories/create');
        }
    }

    public function edit(int $id): string
    {
        $category = $this->categories->findById($id);

        if ($category === null) {
            http_response_code(404);
            return View::render('errors/404', ['title' => 'Catégorie introuvable'], null);
        }

        return View::render('categories/edit', [
            'title'    => 'Modifier une catégorie',
            'category' => $category,
        ]);
    }

    public function update(int $id): never
    {
        try {
            $libelle = trim((string) $this->value('libelle'));
            if ($libelle === '') {
                throw new ValidationException('Le libellé est obligatoire.');
            }

            $this->categories->update($id, [
                'libelle'     => $libelle,
                'description' => trim((string) $this->value('description', '')),
            ]);

            flash('success', 'Catégorie modifiée avec succès.');
            View::redirect('/categories');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/categories/' . $id . '/edit');
        }
    }

    public function delete(int $id): never
    {
        $this->categories->delete($id);
        flash('success', 'Catégorie supprimée.');
        View::redirect('/categories');
    }
}
