<?php
/** @var string $content */
$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? null;
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars((string) $title) . ' · ' : '' ?>Bibliothèque du Savoir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col text-slate-800">

<nav class="bg-slate-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
        <a href="<?= $base ?>/" class="text-xl font-bold tracking-tight">Bibliothèque du Savoir</a>

        <?php if ($user): ?>
        <div class="flex flex-wrap items-center gap-1 text-sm">
            <a href="<?= $base ?>/books" class="px-3 py-2 rounded hover:bg-slate-800">Catalogue</a>
            <a href="<?= $base ?>/my-borrows" class="px-3 py-2 rounded hover:bg-slate-800">Mes emprunts</a>

            <?php if ($role === 'Admin' || $role === 'Bibliothecaire'): ?>
                <a href="<?= $base ?>/categories" class="px-3 py-2 rounded hover:bg-slate-800">Catégories</a>
                <a href="<?= $base ?>/borrows" class="px-3 py-2 rounded hover:bg-slate-800">Emprunts</a>
            <?php endif; ?>

            <?php if ($role === 'Admin'): ?>
                <a href="<?= $base ?>/users" class="px-3 py-2 rounded hover:bg-slate-800">Utilisateurs</a>
            <?php endif; ?>

            <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold
                <?= match ($role) {
                    'Admin' => 'bg-red-500',
                    'Bibliothecaire' => 'bg-amber-500',
                    default => 'bg-emerald-500',
                } ?>">
                <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?> · <?= htmlspecialchars((string) $role) ?>
            </span>

            <a href="<?= $base ?>/logout" class="ml-2 px-3 py-2 rounded bg-slate-800 hover:bg-slate-700">Déconnexion</a>
        </div>
        <?php endif; ?>
    </div>
</nav>

<main class="flex-1 w-full max-w-7xl mx-auto px-4 py-8">

    <?php if ($flash): ?>
    <div class="mb-6 px-4 py-3 rounded-lg text-sm font-medium border
        <?= $flash['type'] === 'success'
            ? 'bg-emerald-50 border-emerald-300 text-emerald-800'
            : 'bg-red-50 border-red-300 text-red-800' ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <?= $content ?>

</main>

<footer class="text-center text-slate-400 text-xs py-6">
    Projet Fil Rouge · POO en PHP · Architecture inspirée de Laravel
</footer>

</body>
</html>
