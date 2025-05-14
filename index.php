<?php
// Servir les fichiers statiques depuis le dossier public

// Obtenir l'URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicFile = __DIR__ . '/public' . $uri;

// Servir directement tous les fichiers existants dans /public
if ($uri !== '/' && file_exists($publicFile) && is_file($publicFile)) {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        // Exécuter les scripts PHP dans /public
        require $publicFile;
        exit;
    }
    // Fichiers statiques (images, CSS, JS, etc.)
    $mime = mime_content_type($publicFile) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    readfile($publicFile);
    exit;
}

// Charger le front controller de public
require __DIR__ . '/public/index.php';
exit;
