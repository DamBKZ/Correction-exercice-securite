<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    public function create()
    {
        if (empty($_SESSION['user'])) {
            set_flash('error', 'Vous devez être connecté pour créer un article.');
            header(REDIRECT_HEADER . base_url('auth/login'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash('error', 'Jeton CSRF invalide. Veuillez réessayer.');
                header(REDIRECT_HEADER . base_url('article/create'));
                exit;
            }

            $title = $_POST['title'] ?? '';
            $title = escape($title); 
            $content = $_POST['content'] ?? '';
            $content = escape($content);
            $user_id = $_SESSION['user']['id'];

            if ($title && $content) {
                $article = new Article();
                $article->create($title, $content, $user_id);

                unset($_POST['title'], $_POST['content']);

                set_flash('success', 'Article publié avec succès !');
                header(REDIRECT_HEADER . base_url());
                exit;
            } else {
                set_flash('error', 'Tous les champs sont requis.');
            }
        }

        $this->render('article/create');
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            set_flash('error', 'Article introuvable.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        $article = new Article();
        $data = $article->find($id);

        if (!$data) {
            set_flash('error', 'Article inexistant.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        $this->render('article/show', ['article' => $data]);
    }

    public function edit()
    {
        if (empty($_SESSION['user'])) {
            set_flash('error', 'Vous devez être connecté.');
            header(REDIRECT_HEADER . base_url('auth/login'));
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            set_flash('error', 'Article introuvable.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        $article = new Article();
        $data = $article->find($id);

        if (!$data) {
            set_flash('error', 'Article inexistant.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        $currentUser = $_SESSION['user'];
        if ($data['user_id'] !== $currentUser['id'] && $currentUser['role'] !== 'admin') {
            set_flash('error', 'Vous ne pouvez pas modifier cet article.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash('error', 'Jeton CSRF invalide. Veuillez réessayer.');
                header(REDIRECT_HEADER . base_url('article/edit&id=' . $id));
                exit;
            }

            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $title = escape($title);
            $content = escape($content);
            if ($title && $content) {
                $article->update($id, $title, $content);

                unset($_session['csrf_token']);
                
                set_flash('success', 'Article modifié avec succès.');
                header(REDIRECT_HEADER . base_url('article/show&id=' . $id));
                exit;
            }

            set_flash('error', 'Tous les champs sont requis.');
        }

        $this->render('article/edit', ['article' => $data]);
    }

}
