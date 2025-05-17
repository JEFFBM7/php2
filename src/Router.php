<?php
namespace App;

// Pas besoin de require l'autoloader ici, il est déjà chargé dans le fichier d'entrée
// require_once __DIR__ . '/../vendor/autoload.php';

use AltoRouter;
use PDO;
use App\Model\Connection;
use App\Util\Logger;
use App\Util\Security;
use Whoops\Run as WhoopsRun;
use Whoops\Handler\PrettyPageHandler;

class Router {
    private $router;
    private $viewsPath;

    public function __construct($viewsPath) {
        $this->router = new AltoRouter();
        // Désactiver complètement le basePath pour éviter les problèmes de correspondance
        $this->router->setBasePath('');
        $this->viewsPath = $viewsPath;
        
        // Initialiser le gestionnaire d'erreurs en mode développement
        if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
            $this->initErrorHandler();
        }
    }
    
    /**
     * Initialise le gestionnaire d'erreurs Whoops
     */
    private function initErrorHandler() {
        $whoops = new WhoopsRun();
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();
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

    /**
     * Obtenir l'ID de l'utilisateur connecté s'il existe
     * @return int|null ID de l'utilisateur ou null
     */
    private function getUserId() {
        $security = Security::getInstance();
        return $security->getCurrentUserId();
    }

    /**
     * Vérifier si l'utilisateur est connecté
     * @return bool True si l'utilisateur est connecté
     */
    private function isLoggedIn() {
        $security = Security::getInstance();
        return $security->isLoggedIn();
    }

    /**
     * Obtenir les informations de l'utilisateur connecté
     * @return array|null Informations de l'utilisateur ou null
     */
    private function getUserInfo() {
        $security = Security::getInstance();
        return $security->getCurrentUser();
    }

    public function handleRequest() {
        // Démarrer ou récupérer la session si elle n'est pas déjà active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialiser le logger
        $logger = Logger::getInstance();

        // Débogage: afficher l'URL actuelle et les données de session
        $currentUrl = $_SERVER['REQUEST_URI'];
        
        // Nettoyer l'URL pour retirer les éventuels /public/index.php/ du début et les slashes supplémentaires
        $currentUrl = preg_replace('#^/public/index\.php/?#', '/', $currentUrl);
        
        // Vérifier si l'URL contient '/index.php' sans 'public/' au début
        if (strpos($currentUrl, '/index.php') === 0) {
            $currentUrl = preg_replace('#^/index\.php/?#', '/', $currentUrl);
        }
        
        // Éviter les doubles slashes
        $currentUrl = preg_replace('#/+#', '/', $currentUrl);
        
        // S'assurer que l'URL commence par /
        if (empty($currentUrl) || $currentUrl[0] !== '/') {
            $currentUrl = '/' . $currentUrl;
        }
        
        // Log avec notre classe Logger
        $logger->debug("URL nettoyée: " . $currentUrl);
        $logger->debug("Session ID: " . session_id());
        $logger->debug("Session user_id: " . ($this->getUserId() ?? 'non connecté'));
        
        // Réinitialiser l'URL actuelle pour le routeur
        $_SERVER['REQUEST_URI'] = $currentUrl;

        // Force le routeur à faire la correspondance
        $match = $this->router->match();
        
        $logger = Logger::getInstance();
        $logger->debug("Match trouvé: " . ($match ? 'oui' : 'non'));
        if ($match) {
            $logger->debug("Vue cible: " . (is_string($match['target']) ? $match['target'] : 'Closure'));
        }

        if ($match) {
            // Si le target est un closure, l'exécuter directement
            if (is_callable($match['target']) && !is_string($match['target'])) {
                return call_user_func_array($match['target'], $match['params']);
            }
            
            $view = $match['target'];
            $viewFile = $this->viewsPath . DIRECTORY_SEPARATOR . $view . '.php';
            
            $logger = Logger::getInstance();
            $logger->debug("Tentative de chargement du fichier: " . $viewFile);
            
            if (file_exists($viewFile)) {
                $logger->debug("Le fichier de vue existe bien");
                // Démarrage du buffer de sortie
                ob_start();
                
                // Extraire les paramètres pour les rendre disponibles dans la vue
                $params = $match['params'] ?? [];
                $logger->debug("Paramètres transmis à la vue: " . json_encode($params));
                
                // Inclusion du fichier de vue
                include_once $viewFile;
                
                // Récupération du contenu du buffer
                $content = ob_get_clean();
                
                // Vérifier si l'utilisateur est connecté
                $isLoggedIn = $this->isLoggedIn();
                $userInfo = $isLoggedIn ? $this->getUserInfo() : null;
                $logger->debug("Utilisateur connecté: " . ($isLoggedIn ? 'oui' : 'non'));
                
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
                $logger = Logger::getInstance();
                $logger->error("Erreur: Fichier de vue non trouvé: " . $viewFile);
                $this->show404();
            }
        } else {
            $logger = Logger::getInstance();
            $logger->warning("Aucune route correspondante trouvée pour: " . $currentUrl);
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
    
        $currentPath = $_SERVER['REQUEST_URI'];
        
        // Inclusion du layout avec le contenu 404
        include_once $this->viewsPath . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'dafault.php';
    }
}