<?php
require_once __DIR__ . '/../vendor/autoload.php';
$router = new App\Router(__DIR__ . '/../views');

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
$router
       ->get('/',       'index',   'home')
       ->get('/about',  'about',   'about')
       ->get('/contact','contact', 'contact')
       ->get('/produit','produit', 'produit')
       ->post('/add_produit', 'add_produit', 'add_produit')
       ->get('/login', 'login', 'login')
       ->post('/login', 'login', 'login_post')
       ->get('/search', 'search', 'search') 
       ->get('/sing', 'sing', 'sing_in')
       ->get('/profil', 'profil', 'profile') 
       ->get('/logout', 'logout', 'logout') 
       ->run();