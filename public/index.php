<?php
// Si l'accès se fait directement, charger la configuration
if (!defined('APP_ROOT')) {
    // Charger l'autoloader de Composer
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Charger la configuration centralisée
    require_once __DIR__ . '/../config.php';
}

// Servir les fichiers statiques existants dans /public
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Gérer la requête du favicon pour éviter la 404
if ($uri === '/favicon.ico') {
    header('Content-Type: image/png');
    readfile(__DIR__ . '/images/logo1.png');
    exit;
}

// Fichier cible
$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        // Exécuter directement les scripts PHP présents dans /public
        require $file;
        exit;
    }
    // Fichiers statiques (images, CSS, JS, etc.)
    $mime = mime_content_type($file) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    readfile($file);
    exit;
}

use Whoops\Run as WhoopsRun;
use Whoops\Handler\PrettyPageHandler;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Router.php';
$whoops = new WhoopsRun();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

$router = new Router(__DIR__ . '/../views');

$router
       ->get('/',            'index',        'home')
       ->post('/',           'index',        'home_post')
       ->get('/about',       'about',        'about')
       ->get('/contact',     'contact',      'contact')
       ->get('/produit',     'produit',      'produit')
       ->get('/produit/[i:id]', 'produit_detail', 'produit_detail')  // Route pour les détails d'un produit
       ->get('/login',       'login',        'login')
       ->post('/login',      'login',        'login_post')
       ->get('/search',      'search',       'search')
       ->get('/singup',        'singup',         'sing_in')           // Route GET pour afficher le formulaire d'inscription
       ->post('/singup',       'singup',         'sing_in_post')      //
       ->get('/profil',      'profil',       'profil')            // Route GET pour afficher le profil
       ->post('/profil',     'profil',       'profil_post_actions')     // Route POST pour traiter les actions du profil
       ->get('/logout',      'logout',       'logout')         // Route pour se déconnecter
       ->get('/add_produit', 'add_produit',  'add_produit')    // Route GET pour afficher le formulaire d'ajout
       ->post('/add_produit','add_produit',  'add_produit_post')
       ->get('/edit_produit', 'edit_produit', 'edit_produit')        // Route GET pour afficher le formulaire de modification
       ->post('/edit_produit','edit_produit', 'edit_produit_post')  // Route POST pour traiter la modification
       ->get('/edit_profile', 'edit_profile', 'edit_profile')       // Route GET pour afficher le formulaire de modification du profil 
       ->post('/edit_profile','edit_profile', 'edit_profile_post')  // Route POST pour traiter la modification du profil
       ->get('/panier',      'panier',       'panier')              // Nouvelle route pour le panier
       ->get('/adminer',     'adminer',      'adminer')     // Route pour Adminer
       ->post('/panier', 'panier', 'panier_post')
       ->run();