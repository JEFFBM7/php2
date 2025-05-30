<?php
// Ce script sert de proxy pour rediriger les requêtes vers tutorial.js
// vers l'emplacement correct du fichier

$correctPath = __DIR__ . '/../assets/js/tutorial.js';

if (file_exists($correctPath)) {
    // Définir le bon type MIME
    header('Content-Type: application/javascript');
    
    // Mettre en cache le fichier pour éviter des requêtes inutiles
    header('Cache-Control: max-age=86400'); // 24 heures
    
    // Lire et afficher le contenu du fichier
    readfile($correctPath);
} else {
    // Le fichier n'existe pas, envoyer une erreur 404
    header('HTTP/1.1 404 Not Found');
    echo "// Le fichier tutorial.js n'a pas été trouvé.";
}
