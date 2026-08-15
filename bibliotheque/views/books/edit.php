<?php
/** @var \App\Models\Book $book */
/** @var \App\Models\Category[] $categories */
?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Modifier le livre</h1>

    <form method="post" action="<?= $base ?>/books/<?= $book->id ?>/update" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-4">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="isbn" class="block text-sm font-medium mb-1">ISBN</label>
                <input type="text" id="isbn" name="isbn" required value="<?= htmlspecialchars($book->isbn) ?>"
                       class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label for="categorie_id" class="block text-sm font-medium mb-1">Catégorie</label>
                <select id="categorie_id" name="categorie_id"
                        class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    <option value="">Aucune</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->id ?>"
                            <?= $book->categorieId === $category->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category->libelle) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="titre" class="block text-sm font-medium mb-1">Titre</label>
            <input type="text" id="titre" name="titre" required value="<?= htmlspecialchars($book->titre) ?>"
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label for="auteur" class="block text-sm font-medium mb-1">Auteur</label>
            <input type="text" id="auteur" name="auteur" required value="<?= htmlspecialchars($book->auteur) ?>"
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium mb-1">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none"><?= htmlspecialchars($book->description) ?></textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="date_publication" class="block text-sm font-medium mb-1">Date de publication</label>
                <input type="date" id="date_publication" name="date_publication"
                       value="<?= htmlspecialchars((string) $book->datePublication) ?>"
                       class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label for="quantite" class="block text-sm font-medium mb-1">Quantité</label>
                <input type="number" id="quantite" name="quantite" min="0" value="<?= $book->quantite ?>" required
                       class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label for="couverture" class="block text-sm font-medium mb-1">Couverture (image)</label>

            <?php if ($book->couverture): ?>
            <div class="mb-2 flex items-center gap-3">
                <img src="<?= image_url($book->couverture) ?>" alt="<?= htmlspecialchars($book->titre) ?>"
                     class="w-14 h-20 object-cover rounded border border-slate-200">
                <span class="text-xs text-slate-400">Couverture actuelle — laissez vide pour la conserver.</span>
            </div>
            <?php endif; ?>

            <input type="file" id="couverture" name="couverture" accept="image/png,image/jpeg,image/gif,image/webp"
                   class="w-full px-3 py-2 rounded border border-slate-300 text-sm file:mr-3 file:rounded file:border-0 file:bg-sky-50 file:px-3 file:py-1.5 file:text-sky-700 file:hover:bg-sky-100">
            <p class="mt-1 text-xs text-slate-400">JPG, PNG, GIF ou WebP — 2 Mo maximum.</p>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="<?= $base ?>/books" class="text-sm text-slate-500 hover:text-slate-700">Annuler</a>
            <button type="submit" class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-medium">
                Enregistrer
            </button>
        </div>
    </form>
</div>
