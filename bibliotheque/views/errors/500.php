<?php /** @var string $message */ ?>
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center">
    <p class="text-6xl font-bold text-red-500">500</p>
    <h1 class="text-2xl font-bold mt-4">Erreur interne du serveur</h1>
    <p class="text-slate-500 mt-2">Une erreur inattendue est survenue.</p>
    <pre class="mt-4 p-4 rounded bg-slate-100 text-left text-xs max-w-xl overflow-auto"><?= htmlspecialchars($message) ?></pre>
    <a href="<?= $base ?>/" class="mt-6 px-4 py-2 rounded bg-slate-800 hover:bg-slate-700 text-white text-sm">
        Retour à l'accueil
    </a>
</div>
