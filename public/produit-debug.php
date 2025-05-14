<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Produit;
use AltoRouter;

header('Content-Type: text/plain');

echo "Diagnostic de la page produit_detail.php\n";
echo "======================================\n\n";

// Simuler le comportement du routeur
$router = new AltoRouter();
$router->setBasePath('');
$router->map('GET', '/produit/[i:id]', 'produit_detail', 'produit_detail');

// Récupérer l'URL actuelle
$requestUrl = $_SERVER['REQUEST_URI'];
echo "URL demandée: " . $requestUrl . "\n\n";

// Nettoyer l'URL pour retirer les éventuels /public/index.php/ du début et les slashes supplémentaires
$cleanUrl = preg_replace('#^/public/index\.php/?#', '/', $requestUrl);

// Vérifier si l'URL contient '/index.php' sans 'public/' au début
if (strpos($cleanUrl, '/index.php') === 0) {
    $cleanUrl = preg_replace('#^/index\.php/?#', '/', $cleanUrl);
}

// Éviter les doubles slashes
$cleanUrl = preg_replace('#/+#', '/', $cleanUrl);

// S'assurer que l'URL commence par /
if (empty($cleanUrl) || $cleanUrl[0] !== '/') {
    $cleanUrl = '/' . $cleanUrl;
}

echo "URL nettoyée: " . $cleanUrl . "\n\n";

// Tester si le matching route fonctionne
$match = $router->match($cleanUrl);

echo "Résultat du matching de route:\n";
if ($match) {
    print_r($match);
    
    // Vérifier si l'ID est présent et récupérer le produit
    if (isset($match['params']['id'])) {
        $productId = (int)$match['params']['id'];
        echo "\nID du produit trouvé: " . $productId . "\n";
        
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Récupérer les informations du produit
            $stmtProduit = $pdo->prepare('SELECT * FROM produit WHERE id = :id');
            $stmtProduit->execute(['id' => $productId]);
            $produit = $stmtProduit->fetchObject(Produit::class);
            
            if ($produit) {
                echo "Produit trouvé dans la base de données!\n";
                echo "Nom du produit: " . $produit->getNom() . "\n";
                echo "Prix: " . $produit->getPrix() . " " . $produit->getDevis() . "\n";
            } else {
                echo "Aucun produit trouvé avec l'ID " . $productId . "\n";
            }
        } catch (\PDOException $e) {
            echo "Erreur de base de données: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "Aucune route correspondante trouvée.\n";
    echo "Vérifier que l'URL est dans le format /produit/{id}\n";
}
