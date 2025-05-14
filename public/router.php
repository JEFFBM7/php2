<?php
/**
 * Router principal pour gérer les ressources statiques
 * Ce fichier résout les problèmes de chemins des fichiers statiques
 */

// Chemins à intercepter et leurs destinations
$staticMappings = [
    '/images/' => __DIR__ . '/images/',
    '/js/' => __DIR__ . '/js/'
];

// Récupère le chemin de la requête
$requestUri = $_SERVER['REQUEST_URI'];

// Vérifie si la requête correspond à l'un des préfixes à intercepter
foreach ($staticMappings as $prefix => $targetDir) {
    if (strpos($requestUri, $prefix) === 0) {
        $relativePath = substr($requestUri, strlen($prefix));
        $filePath = $targetDir . $relativePath;
        
        // Si le fichier existe, on le sert
        if (file_exists($filePath) && is_file($filePath)) {
            // Détermine le type MIME
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $contentType = 'application/octet-stream'; // Par défaut
            
            // Mappings MIME courants
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'html' => 'text/html',
                'txt' => 'text/plain'
            ];
            
            if (isset($mimeTypes[strtolower($extension)])) {
                $contentType = $mimeTypes[strtolower($extension)];
            }
            
            // Définir les en-têtes et servir le fichier
            header("Content-Type: $contentType");
            header('Cache-Control: max-age=86400, public'); // Cache for 24h
            readfile($filePath);
            exit;
        }
        
        // Si on cherche une image qui n'existe pas, générer une image par défaut
        if ($prefix === '/images/' && strpos($extension, 'png') !== false || 
            strpos($extension, 'jpg') !== false || 
            strpos($extension, 'jpeg') !== false || 
            strpos($extension, 'gif') !== false) {
            
            header('Content-Type: image/png');
            // Créer une image "non disponible"
            $width = 200;
            $height = 150;
            $img = imagecreatetruecolor($width, $height);
            $bgColor = imagecolorallocate($img, 240, 240, 240);
            $textColor = imagecolorallocate($img, 180, 180, 180);
            $borderColor = imagecolorallocate($img, 200, 200, 200);
            imagefilledrectangle($img, 0, 0, $width, $height, $bgColor);
            imagerectangle($img, 0, 0, $width - 1, $height - 1, $borderColor);
            $text = "Image non disponible";
            $fontSize = 3;
            $textWidth = imagefontwidth($fontSize) * strlen($text);
            $textHeight = imagefontheight($fontSize);
            $textX = ($width - $textWidth) / 2;
            $textY = ($height - $textHeight) / 2;
            imagestring($img, $fontSize, $textX, $textY, $text, $textColor);
            imagepng($img);
            imagedestroy($img);
            exit;
        }
        
        // Si le fichier n'existe pas et n'est pas une image, renvoyer une erreur 404
        header("HTTP/1.0 404 Not Found");
        echo "Fichier non trouvé: " . htmlspecialchars($requestUri);
        exit;
    }
}

// Si la requête n'est pas pour un fichier statique, on continue normalement
// Cela permet au routeur PHP de gérer les routes normalement
// En utilisant cette approche, cette ligne ne sera jamais appelée car le serveur PHP
// va passer directement au fichier demandé s'il ne correspond pas aux patterns ci-dessus
return false;
?>
