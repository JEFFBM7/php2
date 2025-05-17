<?php
namespace App\Util;

use App\Model\Connection;
use PDO;
use App\Util\Logger;

/**
 * Classe Security pour gérer l'authentification et la sécurité
 */
class Security {
    private static $instance = null;
    private $logger;
    
    /**
     * Constructeur privé (pattern Singleton)
     */
    private function __construct() {
        $this->logger = Logger::getInstance();
    }
    
    /**
     * Obtenir l'instance unique de Security
     * 
     * @return Security
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Hasher un mot de passe
     * 
     * @param string $password Le mot de passe en clair
     * @return string Le mot de passe hashé
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Vérifier un mot de passe
     * 
     * @param string $password Le mot de passe en clair
     * @param string $hash Le hash stocké
     * @return bool Si le mot de passe correspond au hash
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Authentifier un utilisateur
     * 
     * @param string $login Login de l'utilisateur (généralement son email ou nom)
     * @param string $password Mot de passe en clair
     * @return array|false Les données de l'utilisateur si authentifié, false sinon
     */
    public function login($login, $password) {
        try {
            $pdo = Connection::getInstance();
            
            // Recherche de l'utilisateur par son login
            $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE nom = :login');
            $stmt->execute(['login' => $login]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Utilisateur non trouvé
            if (!$user) {
                $this->logger->warning("Tentative de connexion échouée: utilisateur '$login' non trouvé");
                return false;
            }
            
            // Vérification du mot de passe
            if ($this->verifyPassword($password, $user['password'])) {
                // Enregistrement du succès dans les logs
                $this->logger->info("Connexion réussie pour l'utilisateur: " . $user['id']);
                
                // Démarrer la session si nécessaire
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Stocker l'ID de l'utilisateur en session
                $_SESSION['user_id'] = $user['id'];
                
                return $user;
            }
            
            $this->logger->warning("Tentative de connexion échouée: mot de passe incorrect pour '$login'");
            return false;
            
        } catch (\PDOException $e) {
            $this->logger->error("Erreur lors de la tentative de connexion: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Déconnecter l'utilisateur actuel
     * 
     * @return bool True si la déconnexion a réussi
     */
    public function logout() {
        // Démarrer la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Stockage de l'ID pour le log
        $userId = $_SESSION['user_id'] ?? 'inconnu';
        
        // Destruction de la session
        $_SESSION = [];
        
        // Destruction du cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruction complète de la session
        session_destroy();
        
        $this->logger->info("Déconnexion de l'utilisateur: " . $userId);
        
        return true;
    }
    
    /**
     * Vérifier si l'utilisateur actuel est connecté
     * 
     * @return bool True si l'utilisateur est connecté
     */
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
    }
    
    /**
     * Obtenir l'ID de l'utilisateur actuellement connecté
     * 
     * @return int|null L'ID de l'utilisateur ou null s'il n'est pas connecté
     */
    public function getCurrentUserId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Obtenir les informations de l'utilisateur actuellement connecté
     * 
     * @return array|null Les données de l'utilisateur ou null s'il n'est pas connecté
     */
    public function getCurrentUser() {
        $userId = $this->getCurrentUserId();
        
        if (!$userId) {
            return null;
        }
        
        try {
            $pdo = Connection::getInstance();
            
            $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
            $stmt->execute(['id' => $userId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            $this->logger->error("Erreur lors de la récupération des informations utilisateur: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Nettoie et échappe les données entrées par l'utilisateur
     * 
     * @param mixed $data Données à nettoyer
     * @return mixed Données nettoyées
     */
    public function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
            return $data;
        }
        
        // Convertit les caractères spéciaux en entités HTML
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Vérifie si une chaîne contient potentiellement du code malveillant
     * 
     * @param string $string Chaîne à vérifier
     * @return bool True si la chaîne semble suspecte
     */
    public function isSuspiciousInput($string) {
        $patterns = [
            '/(<script|<\/script)/i',     // Tags script
            '/(document\.cookie)/i',      // Manipulation de cookies
            '/(eval\s*\()/i',            // Eval
            '/(\bexec\b|\bshell_exec\b)/i', // Commandes système
            '/(\bSELECT\b.*\bFROM\b|\bUNION\b|\bINSERT\b|\bDELETE\b|\bDROP\b)/i' // SQL injection basique
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $string)) {
                $this->logger->warning("Entrée suspecte détectée: " . $string);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Génère un jeton CSRF pour protéger les formulaires
     * 
     * @return string Jeton CSRF
     */
    public function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        
        return $token;
    }
    
    /**
     * Vérifie si le jeton CSRF fourni est valide
     * 
     * @param string $token Jeton à vérifier
     * @return bool True si le jeton est valide
     */
    public function verifyCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $this->logger->warning("Validation CSRF échouée");
            return false;
        }
        
        // Supprimer le token après vérification (usage unique)
        unset($_SESSION['csrf_token']);
        
        return true;
    }
}
