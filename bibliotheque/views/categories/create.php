<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">Nouvelle catégorie</h1>

    <form method="post" action="<?= $base ?>/categories" class="bg-white rounded-xl shadow p-6 space-y-4">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div>
            <label for="libelle" class="block text-sm font-medium mb-1">Libellé</label>
            <input type="text" id="libelle" name="libelle" required
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium mb-1">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none"></textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="<?= $base ?>/categories" class="text-sm text-slate-500 hover:text-slate-700">Annuler</a>
            <button type="submit" class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-medium">
                Créer la catégorie
            </button>
        </div>
    </form>
</div>
