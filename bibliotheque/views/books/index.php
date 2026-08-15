<?php
/** @var \App\Models\Book[] $books */
/** @var \App\Models\Category[] $categories */
/** @var string $term */
/** @var mixed $categoryId */
/** @var string $filter */
$role = $_SESSION['user']['role'] ?? null;
$canManage = in_array($role, ['Admin', 'Bibliothecaire'], true);
?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Catalogue des livres</h1>
    <?php if ($canManage): ?>
    <a href="<?= $base ?>/books/create" class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-medium text-sm">
        Nouveau livre
    </a>
    <?php endif; ?>
</div>

<form method="get" action="<?= $base ?>/books" class="mb-6 grid sm:grid-cols-4 gap-2">
    <input type="text" name="q" value="<?= htmlspecialchars($term) ?>" placeholder="Titre, auteur, ISBN…"
           class="px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">

    <select name="categorie" class="px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        <option value="">Toutes les catégories</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category->id ?>" <?= (string) $categoryId === (string) $category->id ? 'selected' : '' ?>>
                <?= htmlspecialchars($category->libelle) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="dispo" class="px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Tous les livres</option>
        <option value="disponible" <?= $filter === 'disponible' ? 'selected' : '' ?>>Disponibles</option>
        <option value="indisponible" <?= $filter === 'indisponible' ? 'selected' : '' ?>>Indisponibles</option>
    </select>

    <button type="submit" class="px-4 py-2 rounded bg-slate-800 hover:bg-slate-700 text-white text-sm">Filtrer</button>
</form>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
            <tr>
                <th class="px-4 py-3">Couverture</th>
                <th class="px-4 py-3">Titre</th>
                <th class="px-4 py-3">Auteur</th>
                <th class="px-4 py-3">Catégorie</th>
                <th class="px-4 py-3">Quantité</th>
                <th class="px-4 py-3">Statut</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($books as $book): ?>
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3">
                    <?php if ($book->couverture): ?>
                    <img src="<?= image_url($book->couverture) ?>" alt="<?= htmlspecialchars($book->titre) ?>"
                         class="w-9 h-12 object-cover rounded border border-slate-200">
                    <?php else: ?>
                    <span class="inline-block w-9 h-12 rounded border border-dashed border-slate-200 bg-slate-50"></span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3">
                    <a href="<?= $base ?>/books/<?= $book->id ?>" class="font-medium hover:text-sky-700">
                        <?= htmlspecialchars($book->titre) ?>
                    </a>
                </td>
                <td class="px-4 py-3"><?= htmlspecialchars($book->auteur) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars((string) $book->categorieLibelle) ?></td>
                <td class="px-4 py-3"><?= $book->quantite ?></td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        <?= $book->disponible()
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-red-100 text-red-700' ?>">
                        <?= $book->statut() ?>
                    </span>
                </td>
                <td class="px-4 py-3 flex gap-2">
                    <a href="<?= $base ?>/books/<?= $book->id ?>"
                       class="px-3 py-1 rounded bg-slate-100 hover:bg-slate-200 text-xs">Voir</a>

                    <?php if ($book->disponible()): ?>
                    <form method="post" action="<?= $base ?>/books/<?= $book->id ?>/borrow">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <button type="submit" class="px-3 py-1 rounded bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs">
                            Emprunter
                        </button>
                    </form>
                    <?php endif; ?>

                    <?php if ($canManage): ?>
                        <a href="<?= $base ?>/books/<?= $book->id ?>/edit"
                           class="px-3 py-1 rounded bg-slate-100 hover:bg-slate-200 text-xs">Modifier</a>
                        <form method="post" action="<?= $base ?>/books/<?= $book->id ?>/delete"
                              onsubmit="return confirm('Supprimer ce livre ?');">
                            <input type="hidden" name="_token" value="<?= csrf() ?>">
                            <button type="submit" class="px-3 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 text-xs">
                                Supprimer
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($books)): ?>
        <p class="p-6 text-center text-slate-400">Aucun livre ne correspond à vos critères.</p>
    <?php endif; ?>
</div>
