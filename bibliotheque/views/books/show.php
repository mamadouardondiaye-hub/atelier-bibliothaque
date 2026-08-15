<?php /** @var \App\Models\Book $book */ ?>
<div class="max-w-3xl">
    <a href="<?= $base ?>/books" class="text-sm text-slate-500 hover:text-slate-700">&larr; Retour au catalogue</a>

    <div class="bg-white rounded-xl shadow p-6 mt-4">
        <div class="flex items-start gap-6">
            <?php if ($book->couverture): ?>
            <img src="<?= image_url($book->couverture) ?>" alt="<?= htmlspecialchars($book->titre) ?>"
                 class="w-36 h-52 object-cover rounded-lg border border-slate-200 shrink-0">
            <?php endif; ?>

            <div class="flex-1">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold"><?= htmlspecialchars($book->titre) ?></h1>
                        <p class="text-slate-500"><?= htmlspecialchars($book->auteur) ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold shrink-0
                        <?= $book->disponible() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?>">
                        <?= $book->statut() ?>
                    </span>
                </div>

                <dl class="mt-6 grid sm:grid-cols-2 gap-4 text-sm">
                    <div class="border-b border-slate-100 pb-2">
                        <dt class="text-slate-500 text-xs uppercase">ISBN</dt>
                        <dd class="font-medium"><?= htmlspecialchars($book->isbn) ?></dd>
                    </div>
                    <div class="border-b border-slate-100 pb-2">
                        <dt class="text-slate-500 text-xs uppercase">Catégorie</dt>
                        <dd class="font-medium"><?= htmlspecialchars((string) $book->categorieLibelle) ?></dd>
                    </div>
                    <div class="border-b border-slate-100 pb-2">
                        <dt class="text-slate-500 text-xs uppercase">Date de publication</dt>
                        <dd class="font-medium"><?= htmlspecialchars((string) $book->datePublication) ?></dd>
                    </div>
                    <div class="border-b border-slate-100 pb-2">
                        <dt class="text-slate-500 text-xs uppercase">Exemplaires disponibles</dt>
                        <dd class="font-medium"><?= $book->quantite ?></dd>
                    </div>
                </dl>

                <?php if ($book->description !== ''): ?>
                <div class="mt-6">
                    <h2 class="text-sm font-semibold text-slate-500 uppercase mb-2">Description</h2>
                    <p class="text-slate-700"><?= nl2br(htmlspecialchars($book->description)) ?></p>
                </div>
                <?php endif; ?>

                <div class="mt-8 flex gap-3">
                    <?php if ($book->disponible()): ?>
                    <form method="post" action="<?= $base ?>/books/<?= $book->id ?>/borrow">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <button type="submit" class="px-5 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white font-medium">
                            Emprunter ce livre
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
