<?php
/**
 * Configuration centralisée de l'application
 * 
 * Ce fichier charge les variables d'environnement et définit les constantes globales
 * utilisées dans l'ensemble de l'application.
 */

// Chargement des variables d'environnement si elles ne sont pas déjà chargées
$dotenvFile = __DIR__ . '/.env';
if (file_exists($dotenvFile) && class_exists('\\Dotenv\\Dotenv')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// Autoloader pour les classes utilitaires fréquemment utilisées
spl_autoload_register(function ($className) {
    $classMap = [
        'App\\Util\\Logger' => __DIR__ . '/src/Util/Logger.php',
        'App\\Util\\Security' => __DIR__ . '/src/Util/Security.php',
        'App\\Util\\Helper' => __DIR__ . '/src/Util/Helper.php',
        'App\\Util\\Database' => __DIR__ . '/src/Util/Database.php'
    ];
    
    if (isset($classMap[$className]) && file_exists($classMap[$className])) {
        require_once $classMap[$className];
    }
});

// Configuration du mode d'erreur PHP en fonction de l'environnement
$isDevMode = isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development';
$debugEnabled = isset($_ENV['APP_DEBUG']) ? $_ENV['APP_DEBUG'] === 'true' : false;

if ($isDevMode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Définir les constantes pour les chemins de l'application
define('APP_ROOT', __DIR__);
define('PUBLIC_PATH', APP_ROOT . '/public');
define('VIEWS_PATH', APP_ROOT . '/views');
define('SRC_PATH', APP_ROOT . '/src');
define('ASSETS_PATH', APP_ROOT . '/assets');
define('UPLOADS_PATH', PUBLIC_PATH . '/images/produits');
define('PROFILE_PATH', PUBLIC_PATH . '/images/profile/uploads');

// Configuration de la base de données
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'tp');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASSWORD'] ?? 'root');

// Configuration générale de l'application
define('APP_NAME', 'TrucsPasChers');
define('APP_VERSION', '1.0.0');
// Déterminer le schéma HTTP ou HTTPS
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('APP_URL', $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('DEBUG_MODE', $debugEnabled);

/**
 * Fonction utilitaire pour faciliter le débogage
 * 
 * @param mixed $data Données à afficher
 * @param bool $exit Arrêter l'exécution après affichage
 */
function debug($data, $exit = false) {
    if (DEBUG_MODE) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
        if ($exit) {
            exit;
        }
    }
}

/**
 * Fonction pour écrire dans les logs de l'application
 * 
 * @param string $message Message à logger
 * @param string $level Niveau de log
 */
function log_message($message, $level = 'info') {
    $logger = \App\Util\Logger::getInstance();
    
    switch (strtolower($level)) {
        case 'error':
            $logger->error($message);
            break;
        case 'warning':
            $logger->warning($message);
            break;
        case 'debug':
            $logger->debug($message);
            break;
        case 'info':
        default:
            $logger->info($message);
            break;
    }
}

/**
 * Fonction pour rediriger vers une URL
 * 
 * @param string $url URL relative ou absolue vers laquelle rediriger
 * @param int $status Code de statut HTTP
 * @return void
 */
function redirect($url, $status = 302) {
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = APP_URL . $url;
    }
    header("Location: $url", true, $status);
    exit;
}

/**
 * Convertit une chaîne en URL sécurisée (slug)
 * 
 * @param string $string Chaîne à convertir
 * @return string Slug de la chaîne
 */
function slugify($string) {
    return \App\Util\Helper::slugify($string);
}

/**
 * Échappe les données pour éviter les failles XSS
 * 
 * @param mixed $data Données à échapper
 * @return string Données échappées
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Formater un prix avec le symbole de devise
 * 
 * @param float $price Prix à formater
 * @param string $currency Code de la devise
 * @return string Prix formaté
 */
function format_price($price, $currency = 'EUR') {
    return \App\Util\Helper::formatPrice($price, $currency);
}

/**
 * Tronquer un texte à une longueur donnée
 * 
 * @param string $text Texte à tronquer
 * @param int $length Longueur maximum
 * @param string $append Texte à ajouter si tronqué
 * @return string Texte tronqué
 */
function truncate($text, $length = 100, $append = '...') {
    return \App\Util\Helper::truncate($text, $length, $append);
}

/**
 * Formater une date
 * 
 * @param string $date Date à formater
 * @param string $format Format souhaité
 * @return string Date formatée
 */
function format_date($date, $format = 'd/m/Y') {
    return \App\Util\Helper::formatDate($date, $format);
}

/**
 * Vérifier si l'utilisateur est connecté
 * 
 * @return bool True si connecté
 */
function is_logged_in() {
    $security = \App\Util\Security::getInstance();
    return $security->isLoggedIn();
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 * 
 * @return int|null ID ou null
 */
function get_current_user_id() {
    $security = \App\Util\Security::getInstance();
    return $security->getCurrentUserId();
}

/**
 * Obtenir les données de l'utilisateur connecté
 * 
 * @return array|null Données ou null
 */
function get_logged_user() {
    $security = \App\Util\Security::getInstance();
    return $security->getCurrentUser();
}

/**
 * Génère un jeton CSRF
 * 
 * @return string Jeton
 */
function csrf_token() {
    $security = \App\Util\Security::getInstance();
    return $security->generateCsrfToken();
}

/**
 * Génère un champ caché contenant un jeton CSRF
 * 
 * @return string Markup HTML
 */
function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}
