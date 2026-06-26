<h2 class="text-3xl font-bold mb-6">Créer un article</h2>

<form method="POST" class="space-y-4 max-w-2xl">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <input type="text" name="title" placeholder="Titre" class="w-full p-3 border rounded" required>
    <textarea name="content" placeholder="Contenu de l'article" rows="6" class="w-full p-3 border rounded" required></textarea>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Publier</button>
</form>
