<?php
/** @var \App\Models\Borrow[] $borrows */
$userId = (int) ($_SESSION['user']['id'] ?? 0);
$role = $_SESSION['user']['role'] ?? null;
$canReturn = in_array($role, ['Admin', 'Bibliothecaire'], true);
?>
<h1 class="text-2xl font-bold mb-6">Mes emprunts</h1>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-3">Livre</th>
                <th class="px-4 py-3">Date d'emprunt</th>
                <th class="px-4 py-3">Retour prévu</th>
                <th class="px-4 py-3">Retour effectué</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($borrows as $borrow): ?>
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium"><?= htmlspecialchars((string) $borrow->bookTitre) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($borrow->dateEmprunt) ?></td>
                <td class="px-4 py-3">
                    <?php if ($borrow->estEnRetard()): ?>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                            <?= htmlspecialchars($borrow->dateRetourPrevue) ?> (en retard)
                        </span>
                    <?php else: ?>
                        <?= htmlspecialchars($borrow->dateRetourPrevue) ?>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3"><?= htmlspecialchars((string) $borrow->dateRetour) ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        <?= $borrow->estEnCours() ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>">
                        <?= $borrow->estEnCours() ? 'En cours' : 'Retourné' ?>
                    </span>
                </td>
                <td class="px-4 py-3">
                    <?php if ($borrow->estEnCours() && ($canReturn || $borrow->userId === $userId)): ?>
                    <form method="post" action="<?= $base ?>/borrows/<?= $borrow->id ?>/return">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <button type="submit" class="px-3 py-1 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs">
                            Retourner
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($borrows)): ?>
        <p class="p-6 text-center text-slate-400">Vous n'avez pas encore d'emprunt.</p>
    <?php endif; ?>
</div>
