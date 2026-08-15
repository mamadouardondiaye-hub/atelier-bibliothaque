<div class="max-w-md mx-auto mt-10 bg-white rounded-xl shadow p-8">
    <h1 class="text-2xl font-bold text-center mb-2">Connexion</h1>
    <p class="text-center text-slate-500 text-sm mb-6">Accédez à votre espace</p>

    <form method="post" action="<?= $base ?>/login" class="space-y-4">
        <input type="hidden" name="_token" value="<?= csrf() ?>">

        <div>
            <label for="email" class="block text-sm font-medium mb-1">Adresse e-mail</label>
            <input type="email" id="email" name="email" required
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1">Mot de passe</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-3 py-2 rounded border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
            <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
            Se souvenir de moi
        </label>

        <button type="submit"
                class="w-full py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-semibold transition">
            Se connecter
        </button>
    </form>

    <div class="mt-6 p-4 rounded bg-slate-50 text-xs text-slate-500 border border-slate-200">
        <p class="font-semibold text-slate-600 mb-1">Comptes de démonstration</p>
        <p>Admin : admin@biblio.fr / admin123</p>
        <p>Bibliothécaire : biblio@biblio.fr / biblio123</p>
        <p>Lecteur : alice.martin@mail.fr / lecteur123</p>
    </div>
</div>
