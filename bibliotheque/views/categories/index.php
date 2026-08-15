<?php /** @var \App\Models\Category[] $categories */ ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Catégories</h1>
    <a href="<?= $base ?>/categories/create" class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-medium text-sm">
        Nouvelle catégorie
    </a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($categories as $category): ?>
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-bold"><?= htmlspecialchars($category->libelle) ?></h2>
        <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($category->description) ?></p>
        <div class="flex gap-2 mt-4">
            <a href="<?= $base ?>/categories/<?= $category->id ?>/edit"
               class="px-3 py-1 rounded bg-slate-100 hover:bg-slate-200 text-xs">Modifier</a>
            <form method="post" action="<?= $base ?>/categories/<?= $category->id ?>/delete"
                  onsubmit="return confirm('Supprimer cette catégorie ?');">
                <input type="hidden" name="_token" value="<?= csrf() ?>">
                <button type="submit" class="px-3 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 text-xs">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($categories)): ?>
    <p class="text-center text-slate-400 py-10">Aucune catégorie pour le moment.</p>
<?php endif; ?>
