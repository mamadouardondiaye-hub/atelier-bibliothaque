<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Core\View;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function showLogin(): string
    {
        return View::render('auth/login', ['title' => 'Connexion']);
    }

    public function login(): never
    {
        $email = trim((string) $this->value('email', ''));
        $password = (string) $this->value('password', '');
        $remember = (bool) $this->value('remember', false);

        if ($this->auth->attempt($email, $password, $remember)) {
            flash('success', 'Bienvenue ! Vous êtes connecté.');
            View::redirect('/');
        }

        flash('error', 'Identifiants incorrects.');
        View::redirect('/login');
    }

    public function logout(): never
    {
        $this->auth->logout();
        flash('success', 'Vous êtes déconnecté.');
        View::redirect('/login');
    }
}
