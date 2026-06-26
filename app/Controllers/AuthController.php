<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash('error', 'Jeton CSRF invalide. Veuillez réessayer.');
                header(REDIRECT_HEADER . base_url('auth/register'));
                exit;
            }

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                set_flash('error', 'Tous les champs sont requis.');
                $this->render('auth/register');
                return;
            }

            if (!$this->isPasswordStrong($password)) {
                set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.');
                $this->render('auth/register');
                return;
            }

            $user = new User();
            $user->create($username, $password);

            unset($_SESSION['csrf_token']);

            set_flash('success', 'Inscription réussie, vous pouvez vous connecter.');
            header(REDIRECT_HEADER . base_url('auth/login'));
            exit;
        }

        $this->render('auth/register');
    }

    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                set_flash('error', 'Identifiants invalides');
                $this->render('auth/login', ['error' => $error]);
                return;
            }

            // Vérifie si l'utilisateur/IP est temporairement bloqué
            $remainingTime = $this->getRemainingBlockTime($username);

            if ($remainingTime > 0) {
                set_flash('error', 'Trop de tentatives échouées. Réessayez dans ' . ceil($remainingTime / 60) . ' minute(s).');
                $this->render('auth/login', ['error' => $error]);
                return;
            }

            // On journalise la tentative sans jamais écrire le mot de passe
            $log = "[" . date('Y-m-d H:i:s') . "] Tentative de login : user=$username, ip=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";
            file_put_contents(__DIR__ . '/../../logs/login.log', $log, FILE_APPEND);

            $user = new User();
            $result = $user->login($username, $password);

            if ($result) {
                // Nettoie les tentatives échouées
                $this->clearLoginAttempts($username);

                // Empêche la session fixation
                session_regenerate_id(true);

                $_SESSION['user'] = $result;
                //on ne stock plus  le role dans la sessions

                set_flash('success', 'Connexion réussie !');
                header(REDIRECT_HEADER . base_url());
                exit;
            }

            // En cas d'échec, on ajoute une tentative ratée
            $this->recordFailedLogin($username);
            set_flash('error', 'Identifiants invalides');
        }

        $this->render('auth/login', ['error' => $error]);
    }

    public function logout()
    {
        // Supprime les données de session
        $_SESSION = [];

        // Supprime le cookie de session PHPSESSID
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();

        header(REDIRECT_HEADER . base_url());
        exit;
    }


    //poliqtiques de sécurité pour le mot de passe 
    private function isPasswordStrong(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password);
    }

    // Gestion des tentatives de connexion échouées pour prévenir les attaques par force brute

    //stockage des tentatives de connexion échouées dans un fichier JSON pour persistance
    private function getLoginAttemptsFile(): string
    {
        $dir = __DIR__ . '/../../logs';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir . '/login_attempts.json';
    }


    // Génère une clé unique pour chaque utilisateur/IP pour suivre les tentatives de connexion
    private function getLoginKey(string $username): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        return hash('sha256', strtolower($username) . '|' . $ip);
    }

    private function loadLoginAttempts(): array
    {
        $file = $this->getLoginAttemptsFile();

        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }


    // Enregistre les tentatives de connexion échouées dans le fichier JSON
    private function saveLoginAttempts(array $attempts): void
    {
        file_put_contents(
            $this->getLoginAttemptsFile(),
            json_encode($attempts, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }


// Vérifie combien de temps il reste avant que l'utilisateur/IP soit débloqué
    private function getRemainingBlockTime(string $username): int
    {
        $attempts = $this->loadLoginAttempts();
        $key = $this->getLoginKey($username);

        if (!isset($attempts[$key]['blocked_until'])) {
            return 0;
        }

        $remaining = $attempts[$key]['blocked_until'] - time();

        return max(0, $remaining);
    }

    // Enregistre une tentative de connexion échouée et bloque l'utilisateur/IP après 5 tentatives
    private function recordFailedLogin(string $username): void
    {
        $attempts = $this->loadLoginAttempts();
        $key = $this->getLoginKey($username);

        if (!isset($attempts[$key])) {
            $attempts[$key] = [
                'count' => 0,
                'blocked_until' => 0
            ];
        }

        $attempts[$key]['count']++;

        // Après 5 erreurs, blocage pendant 15 minutes
        if ($attempts[$key]['count'] >= 5) {
            $attempts[$key]['blocked_until'] = time() + (15 * 60);
        }

        $this->saveLoginAttempts($attempts);
    }


    // Supprime les tentatives de connexion échouées après une connexion réussie
    private function clearLoginAttempts(string $username): void
    {
        $attempts = $this->loadLoginAttempts();
        $key = $this->getLoginKey($username);

        if (isset($attempts[$key])) {
            unset($attempts[$key]);
            $this->saveLoginAttempts($attempts);
        }
    }
}