<?php
header('Content-Type: text/plain');

echo "URL Debug Information\n";
echo "====================\n\n";

echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "QUERY_STRING: " . $_SERVER['QUERY_STRING'] . "\n\n";

echo "Parsed URL parts:\n";
$parts = parse_url($_SERVER['REQUEST_URI']);
print_r($parts);

echo "\nAll SERVER variables:\n";
foreach ($_SERVER as $key => $value) {
    echo "$key: $value\n";
}

echo "\n\nAttempting to simulate AltoRouter URL processing:\n";
$requestUrl = $_SERVER['REQUEST_URI'];

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

echo "Original URL: $requestUrl\n";
echo "Cleaned URL: $cleanUrl\n";

// Simulation d'un matching de route simple
if (preg_match('#^/produit/(\d+)$#', $cleanUrl, $matches)) {
    echo "\nMatched route: /produit/[i:id]\n";
    echo "Product ID: " . $matches[1] . "\n";
    echo "This should load produit_detail.php\n";
} else {
    echo "\nNo match for product detail route\n";
}
