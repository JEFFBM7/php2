<?php
// Afficher toutes les requêtes HTTP pour identifier où /js/tutorial.js est référencé
header('Content-Type: text/plain');

echo "=== Informations de débogage pour les chemins ===\n\n";
echo "URL demandée: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Chemin du script: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Méthode: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "HTTP Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'Non disponible') . "\n";
echo "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n\n";

echo "=== En-têtes de requête ===\n";
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}

echo "\n=== Variables GET ===\n";
print_r($_GET);

echo "\n=== Variables POST ===\n";
print_r($_POST);

echo "\n=== Backtrace ===\n";
debug_print_backtrace();

// Enregistrer les requêtes dans un fichier journal
$logFile = __DIR__ . '/../path-debug.log';
$logEntry = date('Y-m-d H:i:s') . " | " . $_SERVER['REQUEST_URI'] . " | Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'Non disponible') . "\n";
file_put_contents($logFile, $logEntry, FILE_APPEND);

echo "\n=== Instructions ===\n";
echo "Ce script enregistre toutes les requêtes HTTP dans le fichier path-debug.log.\n";
echo "Pour déboguer, accédez d'abord à votre site principal, puis vérifiez le journal.\n";
