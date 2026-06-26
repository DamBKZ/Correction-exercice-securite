<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sécurité Web & OWASP en pratique - Apprenez à identifier, exploiter et corriger les failles de sécurité dans une vraie application web pédagogique.">
    <meta name="keywords" content="Sécurité Web, OWASP, Vulnérabilités, Application Web, Pédagogique, Apprentissage, Exploitation, Correction, Sécurité Informatique">
    <meta name="author" content="Grade Julien">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?? 'Sécurité Web' ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
<!-- Navbar -->
<nav class="bg-white shadow-md">
    <div class="mx-auto px-6 py-5 flex justify-between items-center">
        <div class="flex gap-8 items-center">
            <a href="<?= base_url() ?>" class="text-2xl font-extrabold text-blue-700 hover:text-blue-900">SécuBlog</a>
            <a href="<?= base_url() ?>" class="text-lg text-gray-700 hover:text-blue-600 transition">Accueil</a>
            <?php if (!empty($_SESSION['user'])): ?>
                <a href="<?= base_url('article/create') ?>" class="text-lg text-gray-700 hover:text-blue-600 transition">Créer un article</a>
            <?php endif; ?>
            <a href="<?= base_url('search') ?>" class="text-lg text-gray-700 hover:text-blue-600 transition">Recherche</a>
            <a href="<?= base_url('tools/hash') ?>" class="text-lg text-gray-700 hover:text-blue-600 transition">Générateur de hash</a>
            <a href="<?= base_url('page/contact') ?>" class="text-lg text-gray-700 hover:text-blue-600 transition">Contactez-nous</a>
            <a href="<?= base_url('page/apropos') ?>" class="text-lg text-gray-700 hover:text-blue-600 transition">À propos</a>
            <a href="<?= base_url('page/mentions') ?>" class="text-lg text-gray-700 hover:text-blue-600 transition">Mentions légales</a>
        </div>
        <div class="flex items-center gap-4">
            <?php if (!empty($_SESSION['user'])): ?>
                <span class="text-md text-gray-600">👤 <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                <a href="<?= base_url('auth/logout') ?>" class="px-4 py-2 bg-red-500 text-white rounded-lg text-md hover:bg-red-600 transition">Déconnexion</a>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="<?= base_url('admin/dashboard') ?>" class="text-md text-purple-700 hover:text-purple-900 font-semibold">
                        Vous êtes Admin !
                    </a>
                <?php endif; ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="<?= base_url('admin/dashboard') ?>" class="text-md text-indigo-600 hover:text-indigo-800 font-semibold">Dashboard Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= base_url('auth/login') ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-md hover:bg-blue-700 transition">Connexion</a>
                <a href="<?= base_url('auth/register') ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-md hover:bg-gray-600 transition">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if ($_GET['url'] ?? 'home' === 'home' || $_GET['url'] === ''): ?>
    <header class="bg-blue-50 py-20 shadow-inner">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-extrabold text-blue-800 mb-4">Sécurité Web & OWASP en pratique</h1>
            <p class="text-xl text-blue-600 max-w-2xl mx-auto">
                Apprenez à identifier, exploiter et corriger les failles de sécurité dans une vraie application web pédagogique.
            </p>
        </div>
    </header>
<?php endif; ?>
<?php foreach (get_flash() as $f): ?>
    <div class="max-w-4xl mx-auto mb-4 px-4 py-3 rounded-lg shadow text-white
              <?= $f['type'] === 'success' ? 'bg-green-500' : ($f['type'] === 'error' ? 'bg-red-500' : 'bg-yellow-500') ?>">
        <?= htmlspecialchars($f['message']) ?>
    </div>
<?php endforeach; ?>

<main class="max-w-6xl mx-auto px-6 py-10">
    <?php if (!empty($content)) echo $content; ?>
</main>
</body>
</html>
