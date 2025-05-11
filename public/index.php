<?php

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
       ->get('/adminer',     'adminer',      'adminer')
       ->get('/admin-tutorial', 'admin_tutorial', 'admin_tutorial')  // Route pour l'administration du tutoriel
       ->get('/tutorial-api', 'tutorial-api', 'tutorial_api')     // API pour récupérer les étapes du tutoriel
       ->post('/save-tutorial-preference', 'save-tutorial-preference', 'save_tutorial_preference')  // API pour sauvegarder les préférences de tutoriel
       ->get('/debug-tutorial', 'debug-tutorial', 'debug_tutorial')  // Page de débogage pour le tutoriel
       ->get('/fix-tutorial', 'fix_tutorial', 'fix_tutorial')     // Page de correctif pour le tutoriel
       ->get('/tutorial-documentation', 'tutorial_documentation', 'tutorial_documentation')  // Documentation du tutoriel
       ->get('/js/tutorial.js', 'js-proxy', 'js_proxy')  // Route spéciale pour rediriger tutorial.js
     
       ->run();