<?php
/** @var \App\Models\User $user */
/** @var array{id:int,libelle:string}[] $roles */
?>
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">Modifier l'utilisateur</h1>

    <form method="post" action="<?= $base ?>/users/<?= $user->id ?>/update" class="bg-white rounded-xl shadow p-6 space-y-4">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="nom" class="block text-sm font-medium mb-1">Nom</label>
                <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($user->nom) ?>"
                       class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label for="prenom" class="block text-sm font-medium mb-1">Prénom</label>
                <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($user->prenom) ?>"
                       class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium mb-1">Adresse e-mail</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user->email) ?>"
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label for="telephone" class="block text-sm font-medium mb-1">Téléphone</label>
            <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars((string) $user->telephone) ?>"
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1">
                Nouveau mot de passe <span class="text-slate-400 font-normal">(laisser vide pour ne pas changer)</span>
            </label>
            <input type="password" id="password" name="password"
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label for="role_id" class="block text-sm font-medium mb-1">Rôle</label>
            <select id="role_id" name="role_id" required
                    class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= $user->roleId === (int) $role['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="<?= $base ?>/users" class="text-sm text-slate-500 hover:text-slate-700">Annuler</a>
            <button type="submit" class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-medium">
                Enregistrer
            </button>
        </div>
    </form>
</div>
