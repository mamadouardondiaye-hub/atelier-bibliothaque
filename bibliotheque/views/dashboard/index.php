<?php
/** @var array $stats */
$user = $_SESSION['user'];
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">Tableau de bord</h1>
        <p class="text-slate-500">Bienvenue, <?= htmlspecialchars($user['prenom']) ?> !</p>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-slate-500 font-medium">Total livres</p>
        <p class="text-3xl font-bold text-sky-700"><?= (int) $stats['total_livres'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-slate-500 font-medium">Livres disponibles</p>
        <p class="text-3xl font-bold text-emerald-600"><?= (int) $stats['livres_disponibles'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-slate-500 font-medium">Utilisateurs</p>
        <p class="text-3xl font-bold text-indigo-600"><?= (int) $stats['total_utilisateurs'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-slate-500 font-medium">Emprunts en cours</p>
        <p class="text-3xl font-bold text-amber-600"><?= (int) $stats['emprunts_en_cours'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-slate-500 font-medium">Emprunts retournés</p>
        <p class="text-3xl font-bold text-slate-600"><?= (int) $stats['emprunts_retournes'] ?></p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-4">Livres les plus empruntés</h2>
        <ol class="space-y-3">
            <?php foreach ($stats['livres_plus_empruntes'] as $book): ?>
                <li class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div>
                        <a href="<?= $base ?>/books/<?= $book->id ?>" class="font-medium hover:text-sky-700">
                            <?= htmlspecialchars($book->titre) ?>
                        </a>
                        <p class="text-xs text-slate-500"><?= htmlspecialchars($book->auteur) ?></p>
                    </div>
                    <span class="text-xs bg-sky-50 text-sky-700 px-2 py-1 rounded-full">
                        <?= (int) $book->totalEmprunts ?> emprunt(s)
                    </span>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-4">Lecteurs les plus actifs</h2>
        <ol class="space-y-3">
            <?php foreach ($stats['lecteurs_plus_actifs'] as $reader): ?>
                <li class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <span class="font-medium"><?= htmlspecialchars($reader->nomComplet()) ?></span>
                    <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-1 rounded-full">
                        <?= (int) $reader->totalEmprunts ?> emprunt(s) en cours
                    </span>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</div>
