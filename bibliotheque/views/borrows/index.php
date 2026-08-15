<?php /** @var \App\Models\Borrow[] $borrows */ ?>
<h1 class="text-2xl font-bold mb-6">Emprunts en cours</h1>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-3">Lecteur</th>
                <th class="px-4 py-3">Livre</th>
                <th class="px-4 py-3">Date d'emprunt</th>
                <th class="px-4 py-3">Retour prévu</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($borrows as $borrow): ?>
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium">
                    <?= htmlspecialchars(($borrow->userPrenom ?? '') . ' ' . ($borrow->userNom ?? '')) ?>
                </td>
                <td class="px-4 py-3"><?= htmlspecialchars((string) $borrow->bookTitre) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($borrow->dateEmprunt) ?></td>
                <td class="px-4 py-3">
                    <?php if ($borrow->estEnRetard()): ?>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                            En retard · <?= htmlspecialchars($borrow->dateRetourPrevue) ?>
                        </span>
                    <?php else: ?>
                        <?= htmlspecialchars($borrow->dateRetourPrevue) ?>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3">
                    <form method="post" action="<?= $base ?>/borrows/<?= $borrow->id ?>/return">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <button type="submit" class="px-3 py-1 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs">
                            Marquer comme retourné
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($borrows)): ?>
        <p class="p-6 text-center text-slate-400">Aucun emprunt en cours.</p>
    <?php endif; ?>
</div>
