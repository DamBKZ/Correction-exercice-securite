<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            set_flash('error', 'Accès réservé aux administrateurs.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        $this->render('admin/dashboard');
    }


    public function users(): void
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            set_flash('error', 'Accès réservé aux administrateurs.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        $users = (new User())->all();
        $this->render('admin/users', ['users' => $users]);
    }

    public function createUser(): void
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            set_flash('error', 'Accès réservé aux administrateurs.');
            header(REDIRECT_HEADER . base_url());
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            if ($username && $password) {
                $userModel = new User();
                $ok = $userModel->createWithRole($username, $password, $role);

                if ($ok) {
                    set_flash('success', "Utilisateur '$username' créé avec succès !");
                    header(REDIRECT_HEADER . base_url('admin/users'));
                    exit;
                } else {
                    set_flash('error', "Le nom d'utilisateur '$username' existe déjà.");
                }
            } else {
                set_flash('error', 'Tous les champs sont requis.');
            }
        }

        $this->render('admin/create-user');
    }

}
