<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use JetBrains\PhpStorm\NoReturn;

class AccountController extends Controller
{
    #[NoReturn]
    public function delete(): void
    {
        if (empty($_SESSION['user'])) {
            set_flash('error', 'Vous devez être connecté.');
            header(REDIRECT_HEADER . base_url('auth/login'));
            exit;
        }

        $currentUser = $_SESSION['user'];

        $id = $_GET['id'] ?? null;

        if (!$id) {
            set_flash('error', 'ID manquant');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

                if ($currentUser['role'] !== 'admin' && (int)$id !== (int)$currentUser['id']) {
            set_flash('error', 'Action non autorisée.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        $user = new User();
        $user->delete($id);

                if ((int)$id === (int)$currentUser['id']) {
            session_destroy();
        }

        set_flash('success', "Compte supprimé avec succès (id $id)");
        header(REDIRECT_HEADER . base_url());
        exit;
    }
}
