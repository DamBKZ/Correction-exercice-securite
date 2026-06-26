<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
<div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-4">Inscription</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
        <input class="w-full p-2 mb-3 border" type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input class="w-full p-2 mb-3 border" type="password" name="password" placeholder="Mot de passe" required>
        <button class="bg-blue-500 text-white px-4 py-2 rounded" type="submit">S'inscrire</button>
    </form>
</div>
</body>
</html>
