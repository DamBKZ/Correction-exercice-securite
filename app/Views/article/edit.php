<h2 class="text-3xl font-bold mb-6">Modifier l'article</h2>

<form method="POST" class="space-y-4 max-w-2xl">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <input type="text" name="title" value="<?= escape($article['title']) ?>" class="w-full p-3 border rounded" required>
    <textarea name="content" rows="6" class="w-full p-3 border rounded" required><?= escape($article['content']) ?></textarea>
    <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded hover:bg-yellow-600">Mettre à jour</button>
</form>
