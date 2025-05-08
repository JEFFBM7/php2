<?php
namespace App;

use AltoRouter;

class Router{
    /**
     * @var All routes
     */
    private $routes ;
    /**
     * @var String Path to the views
     */
    private $viewsPath; 
    public function __construct( $viewsPath){
        $this->viewsPath = $viewsPath;
        $this->routes = new AltoRouter();
    }
    public function get(string $url, string $view, string $name = null): self 
    {
        $this->routes->map('GET', $url, $view, $name);
        return $this;

    }
    public function post(string $url, string $view, string $name = null): self
    {
        $this->routes->map('POST', $url, $view, $name);
        return $this;
    }
    public function run()
     { 
        $match = $this->routes->match();
        if($match){
            $view = $match['target'];
            $params = $match['params'];
            ob_start();
            if(file_exists($this->viewsPath . DIRECTORY_SEPARATOR . $view . '.php')){
                include_once $this->viewsPath . DIRECTORY_SEPARATOR . $view . '.php';
                $content = ob_get_clean();
                // Définit les éléments de navigation et le chemin courant
                $navItems = [
                    '/'        => 'Accueil',
                    '/produit' => 'Produits',
                    '/contact' => 'Contact',
                    '/about'   => 'À propos',
                    '/login'   => 'Connexion',
                    
                    
                ];
                $currentPath = $_SERVER['REQUEST_URI'];
                include_once $this->viewsPath . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'dafault.php';    
            }else{
                throw new \Exception('View not found');
            }
        } else {
            // Page 404
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            ob_start();
            include_once $this->viewsPath . DIRECTORY_SEPARATOR . '404.php';
            $content = ob_get_clean();
           
        }
     }
}