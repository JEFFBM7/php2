<?php
require_once __DIR__ . '/../vendor/autoload.php';
$router = new App\Router(__DIR__ . '/../views');

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
$router
       ->get('/',       'index',   'home')
       ->get('/produit', 'produit', 'produit')
       ->get('/add',    'add_produit', 'add_produit')
       ->get('login', 'login', 'login')
       ->get('/search', 'search', 'search')
       ->get('logout', 'logout', 'logout')
       ->get('/produit/[i:id]', 'produit_detail', 'produit_detail')
       ->get('singup', 'singup', 'singup')
       ->get('contact', 'contact', 'contact')
       ->get('about', 'about', 'about')
       

       ->run();