<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\UserRepositoryInterface;
use Core\View;
use Exceptions\ValidationException;

class UserController extends Controller
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function index(): string
    {
        $term = trim((string) $this->value('q', ''));
        $users = $term === '' ? $this->users->all() : $this->users->search($term);

        return View::render('users/index', [
            'title'  => 'Utilisateurs',
            'users'  => $users,
            'term'   => $term,
        ]);
    }

    public function create(): string
    {
        return View::render('users/create', [
            'title' => 'Nouvel utilisateur',
            'roles' => $this->users->roles(),
        ]);
    }

    public function store(): never
    {
        try {
            $this->validateInput();

            $this->users->create([
                'nom'       => trim((string) $this->value('nom')),
                'prenom'    => trim((string) $this->value('prenom')),
                'email'     => trim((string) $this->value('email')),
                'password'  => password_hash((string) $this->value('password'), PASSWORD_DEFAULT),
                'telephone' => $this->value('telephone') ?: null,
                'role_id'   => (int) $this->value('role_id'),
            ]);

            flash('success', 'Utilisateur créé avec succès.');
            View::redirect('/users');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/users/create');
        }
    }

    public function edit(int $id): string
    {
        $user = $this->users->findById($id);

        if ($user === null) {
            http_response_code(404);
            return View::render('errors/404', ['title' => 'Utilisateur introuvable'], null);
        }

        return View::render('users/edit', [
            'title' => 'Modifier un utilisateur',
            'user'  => $user,
            'roles' => $this->users->roles(),
        ]);
    }

    public function update(int $id): never
    {
        try {
            $this->validateInput();

            $this->users->update($id, [
                'nom'       => trim((string) $this->value('nom')),
                'prenom'    => trim((string) $this->value('prenom')),
                'email'     => trim((string) $this->value('email')),
                'telephone' => $this->value('telephone') ?: null,
                'role_id'   => (int) $this->value('role_id'),
            ]);

            $password = (string) $this->value('password', '');
            if ($password !== '') {
                $this->users->updatePassword($id, $password);
            }

            flash('success', 'Utilisateur modifié avec succès.');
            View::redirect('/users');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
            View::redirectBack('/users/' . $id . '/edit');
        }
    }

    public function delete(int $id): never
    {
        $this->users->delete($id);
        flash('success', 'Utilisateur supprimé.');
        View::redirect('/users');
    }

    private function validateInput(): void
    {
        if (trim((string) $this->value('nom')) === '' || trim((string) $this->value('prenom')) === '') {
            throw new ValidationException('Le nom et le prénom sont obligatoires.');
        }
        if (!filter_var((string) $this->value('email'), FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("L'adresse e-mail est invalide.");
        }
    }
}
