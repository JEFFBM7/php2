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
       ->get('/login',       'login',        'login')
       ->post('/login',      'login',        'login_post')
       ->get('/search',      'search',       'search')
       ->get('/singup',        'singup',         'sing_in')
       ->post('/singup',       'singup',         'sing_in_post')
       ->get('/profil',      'profil',       'profil')
       ->get('/logout',      'logout',       'logout')
       ->get('/add_produit', 'add_produit',  'add_produit')
       ->post('/add_produit','add_produit',  'add_produit_post')
       ->get('/adminer',     'adminer',      'adminer')
     
       ->run();