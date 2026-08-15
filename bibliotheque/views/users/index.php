<?php
/** @var \App\Models\User[] $users */
/** @var string $term */
?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Utilisateurs</h1>
    <a href="<?= $base ?>/users/create" class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-medium text-sm">
        Nouvel utilisateur
    </a>
</div>

<form method="get" action="<?= $base ?>/users" class="mb-6 flex gap-2">
    <input type="text" name="q" value="<?= htmlspecialchars($term) ?>" placeholder="Rechercher (nom, prénom, e-mail)…"
           class="flex-1 px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
    <button type="submit" class="px-4 py-2 rounded bg-slate-800 hover:bg-slate-700 text-white text-sm">Rechercher</button>
</form>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-3">Nom</th>
                <th class="px-4 py-3">E-mail</th>
                <th class="px-4 py-3">Téléphone</th>
                <th class="px-4 py-3">Rôle</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($users as $user): ?>
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($user->nomComplet()) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($user->email) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars((string) $user->telephone) ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        <?= match ($user->role) {
                            'Admin' => 'bg-red-100 text-red-700',
                            'Bibliothecaire' => 'bg-amber-100 text-amber-700',
                            default => 'bg-emerald-100 text-emerald-700',
                        } ?>">
                        <?= htmlspecialchars($user->role) ?>
                    </span>
                </td>
                <td class="px-4 py-3 flex gap-2">
                    <a href="<?= $base ?>/users/<?= $user->id ?>/edit"
                       class="px-3 py-1 rounded bg-slate-100 hover:bg-slate-200 text-xs">Modifier</a>
                    <form method="post" action="<?= $base ?>/users/<?= $user->id ?>/delete"
                          onsubmit="return confirm('Supprimer cet utilisateur ?');">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <button type="submit" class="px-3 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 text-xs">
                            Supprimer
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($users)): ?>
        <p class="p-6 text-center text-slate-400">Aucun utilisateur trouvé.</p>
    <?php endif; ?>
</div>
