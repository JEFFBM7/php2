<?php
namespace App;

require_once __DIR__ . '/../vendor/autoload.php';

use AltoRouter;
use PDO;

class Router {
    private $router;
    private $viewsPath;

    public function __construct($viewsPath) {
        $this->router = new AltoRouter();
        // Désactiver complètement le basePath pour éviter les problèmes de correspondance
        $this->router->setBasePath('');
        $this->viewsPath = $viewsPath;
        
        // La route pour les détails du produit est maintenant définie dans index.php
    }

    /**
     * Ajoute une route GET
     */
    public function get($url, $view, $name) {
        $this->router->map('GET', $url, $view, $name);
        return $this;
    }

    /**
     * Ajoute une route POST
     */
    public function post($url, $view, $name) {
        $this->router->map('POST', $url, $view, $name);
        return $this;
    }

    /**
     * Exécute le routeur
     */
    public function run() {
        $this->handleRequest();
    }

    // Obtenir l'ID de l'utilisateur connecté s'il existe
    private function getUserId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }

    // Vérifier si l'utilisateur est connecté
    private function isLoggedIn() {
        return $this->getUserId() !== null;
    }

    // Obtenir les informations de l'utilisateur connecté
    private function getUserInfo() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
            $stmt->execute(['id' => $this->getUserId()]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur PDO dans getUserInfo: " . $e->getMessage());
            return null;
        }
    }

    public function handleRequest() {
        // Débogage: afficher l'URL actuelle et les données de session
        $currentUrl = $_SERVER['REQUEST_URI'];
        
        // Nettoyer l'URL pour retirer les éventuels /public/index.php/ du début
        $currentUrl = preg_replace('#^/public/index\.php#', '', $currentUrl);
        
        error_log("URL nettoyée: " . $currentUrl);
        error_log("Session ID: " . session_id());
        error_log("Session user_id: " . ($this->getUserId() ?? 'non connecté'));
        
        // Réinitialiser l'URL actuelle pour le routeur
        $_SERVER['REQUEST_URI'] = $currentUrl;

        // Force le routeur à faire la correspondance
        $match = $this->router->match();
        
        error_log("Match trouvé: " . ($match ? 'oui' : 'non'));
        if ($match) {
            error_log("Vue cible: " . $match['target']);
        }

        if ($match) {
            $view = $match['target'];
            $viewFile = $this->viewsPath . DIRECTORY_SEPARATOR . $view . '.php';
            error_log("Tentative de chargement du fichier: " . $viewFile);
            
            if (file_exists($viewFile)) {
                error_log("Le fichier de vue existe bien");
                // Démarrage du buffer de sortie
                ob_start();
                
                // Inclusion du fichier de vue
                include_once $viewFile;
                
                // Récupération du contenu du buffer
                $content = ob_get_clean();
                
                // Vérifier si l'utilisateur est connecté
                $isLoggedIn = $this->isLoggedIn();
                $userInfo = $isLoggedIn ? $this->getUserInfo() : null;
                error_log("Utilisateur connecté: " . ($isLoggedIn ? 'oui' : 'non'));
                
                // Définit les éléments de navigation
                $navItems = [
                    '/'        => 'Accueil',
                    '/produit' => 'Produits',
                    '/contact' => 'Contact',
                    '/about'   => 'À propos',
                ];
                
                // Ajouter les liens de connexion ou de profil selon l'état de connexion
                $isLoggedIn = $this->isLoggedIn();
                if (!$isLoggedIn) {
                    $navItems['/login'] = 'Connexion';
                    $navItems['/singup'] = 'Inscription';
                }
                
                $currentPath = $_SERVER['REQUEST_URI'];
                
                // Inclusion du layout avec le contenu
                include_once $this->viewsPath . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'dafault.php';
            } else {
                error_log("Erreur: Fichier de vue non trouvé: " . $viewFile);
                $this->show404();
            }
        } else {
            error_log("Aucune route correspondante trouvée pour: " . $currentUrl);
            $this->show404();
        }
    }
    
    /**
     * Affiche la page 404
     */
    private function show404() {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        
        ob_start();
        $notFoundFile = $this->viewsPath . DIRECTORY_SEPARATOR . '404.php';
        
        if (file_exists($notFoundFile)) {
            include_once $notFoundFile;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
        }
        
        $content = ob_get_clean();
        
        // Vérifier si l'utilisateur est connecté
        $isLoggedIn = $this->isLoggedIn();
        $userInfo = $isLoggedIn ? $this->getUserInfo() : null;
        
        // Définir les éléments de navigation
        $navItems = [
            '/'        => 'Accueil',
            '/produit' => 'Produits',
            '/contact' => 'Contact',
            '/about'   => 'À propos',
            
        ];
        
        // Ajouter les liens de connexion et d'inscription uniquement si l'utilisateur n'est pas connecté
        if (!$isLoggedIn) {
            $navItems['/login'] = 'Connexion';
            $navItems['/singup'] = 'Inscription';
        }
        
        $currentPath = $_SERVER['REQUEST_URI'];
        
        // Inclusion du layout avec le contenu 404
        include_once $this->viewsPath . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'dafault.php';
    }
}