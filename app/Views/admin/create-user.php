<h2 class="text-3xl font-bold mb-6">Créer un nouvel utilisateur</h2>

<form method="POST" class="space-y-4 max-w-xl">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <label for="username">
        <input id="username" type="text" name="username" placeholder="Nom d'utilisateur" class="w-full p-3 border rounded" required>
    </label>
    <label id for="password">
        <input id="password" type="password" name="password" placeholder="Mot de passe" class="w-full p-3 border rounded" required>
    </label>

    <label>
        <select name="role" class="w-full p-3 border rounded">
            <option value="user">Utilisateur standard</option>
            <option value="admin">Administrateur</option>
        </select>
    </label>

    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Créer l'utilisateur</button>
</form>
