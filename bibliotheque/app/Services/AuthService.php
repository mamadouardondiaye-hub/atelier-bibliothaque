<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\RememberTokenRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class AuthService
{
    public const COOKIE_NAME = 'remember_me';
    public const COOKIE_DUREE = 30 * 24 * 3600; // 30 jours

    public function __construct(
        private UserRepositoryInterface $users,
        private RememberTokenRepositoryInterface $tokens,
    ) {}

    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || !password_verify($password, $user->password)) {
            return false;
        }

        $this->setSession($user);

        if ($remember) {
            $this->rememberUser($user->id);
        }

        return true;
    }

    /**
     * Crée un token de connexion persistante et le dépose dans un cookie.
     * Le token brut est stocké dans le cookie ; seule son empreinte SHA-256
     * est conservée en base (si la base fuit, le token est inutilisable).
     */
    public function rememberUser(int $userId): void
    {
        $this->tokens->deleteByUser($userId);

        $token = bin2hex(random_bytes(32));

        $this->tokens->create(
            $userId,
            hash('sha256', $token),
            date('Y-m-d H:i:s', time() + self::COOKIE_DUREE)
        );

        $this->setRememberCookie($token, time() + self::COOKIE_DUREE);
    }

    /**
     * Tente de reconnecter l'utilisateur à partir du cookie « se souvenir de moi ».
     */
    public function loginFromRememberToken(): bool
    {
        if (!empty($_SESSION['user'])) {
            return true;
        }

        $token = $_COOKIE[self::COOKIE_NAME] ?? '';

        if ($token === '') {
            return false;
        }

        $userId = $this->tokens->findUserIdByHash(hash('sha256', $token));

        if ($userId === null) {
            $this->forgetUser();
            return false;
        }

        $user = $this->users->findById($userId);

        if ($user === null) {
            $this->forgetUser();
            return false;
        }

        $this->setSession($user);
        return true;
    }

    public function logout(): void
    {
        $userId = $_SESSION['user']['id'] ?? null;

        $this->forgetUser();

        if ($userId !== null) {
            $this->tokens->deleteByUser($userId);
        }

        unset($_SESSION['user']);
    }

    /** Supprime le cookie et invalide le token correspondant en base. */
    public function forgetUser(): void
    {
        $token = $_COOKIE[self::COOKIE_NAME] ?? '';

        if ($token !== '') {
            $this->tokens->deleteByHash(hash('sha256', $token));
        }

        $this->setRememberCookie('', time() - 3600);
    }

    /** @return array{id:int,nom:string,prenom:string,email:string,role:string}|null */
    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    private function setSession(User $user): void
    {
        $_SESSION['user'] = [
            'id'     => $user->id,
            'nom'    => $user->nom,
            'prenom' => $user->prenom,
            'email'  => $user->email,
            'role'   => $user->role,
        ];
    }

    private function setRememberCookie(string $value, int $expires): void
    {
        setcookie(self::COOKIE_NAME, $value, [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
