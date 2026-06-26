<?php

namespace App\Controllers;

use App\Core\Controller;

class ToolsController extends Controller
{
    public function fetch(): void
    {
        $url = $_GET['url'] ?? null;



        if (!$url) {
            echo "<p class='text-red-600'>Aucune URL fournie</p>";
            return;
        }

        
        if (!$this->isAllowedUrl($url)) {
            http_response_code(403);
            echo "<p class='text-red-600'>URL interdite ou invalide.</p>";
            return;
        }

        // 💣 SSRF vulnérable
        $content = file_get_contents($url);

        echo "<h2 class='text-xl font-bold mb-4'>Résultat pour : <code>" . htmlspecialchars($url) . "</code></h2>";
        echo "<div class='bg-gray-100 p-4 text-sm font-mono overflow-auto'>" . htmlspecialchars($content) . "</div>";
    }

    public function hash(): void
    {
        $result = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $text = $_POST['text'] ?? '';
            $algo = $_POST['algo'] ?? 'md5';

            if ($text && in_array($algo, ['md5', 'sha1', 'sha256', 'bcrypt'])) {
                switch ($algo) {
                    case 'md5':
                        $result = md5($text);
                        break;
                    case 'sha1':
                        $result = sha1($text);
                        break;
                    case 'sha256':
                        $result = hash('sha256', $text);
                        break;
                    case 'bcrypt':
                        $result = password_hash($text, PASSWORD_BCRYPT);
                        break;
                    default:
                        $result = 'Algorithme non supporté.';
                }
            }
        }

        $this->render('tools/hash', ['result' => $result]);
    }


    // Fonction pour vérifier si l'URL est autorisée
private function isAllowedUrl(string $url): bool
{
    $parts = parse_url($url);

    // Vérifie que le schéma et l'hôte sont présents
    if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
        return false;
    }

    $scheme = strtolower($parts['scheme']);

    // Autorise uniquement les schémas HTTP et HTTPS    
    if (!in_array($scheme, ['http', 'https'], true)) {
        return false;
    }

    $host = strtolower($parts['host']);
// Vérifie que l'hôte n'est pas une adresse IP privée ou locale
    if ($host === 'localhost') {
        return false;
    }

    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return $this->isPublicIp($host);
    }

    $records = dns_get_record($host, DNS_A + DNS_AAAA);

    // Vérifie que l'hôte a au moins un enregistrement DNS public
    if (!$records) {
        return false;
    }

    // Vérifie que toutes les adresses IP retournées sont publiques
    foreach ($records as $record) {
        $ip = $record['ip'] ?? $record['ipv6'] ?? null;

        if (!$ip || !$this->isPublicIp($ip)) {
            return false;
        }
    }

    return true;
}


// Vérifie si une adresse IP est publique
private function isPublicIp(string $ip): bool
{
    return filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    ) !== false;
}
}
