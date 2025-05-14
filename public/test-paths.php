<?php
// Fichier de test pour vérifier les chemins d'accès aux fichiers

// Configuration d'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Liste des chemins à tester
$paths = [
    '/images/logo1.png',
    '/public/images/logo1.png',
    '/js/tutorial.js',
    '/public/js/tutorial.js',
    '/assets/js/tutorial.js'
];

echo '<!DOCTYPE html>
<html>
<head>
    <title>Test des chemins de fichiers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Test des chemins de fichiers</h1>
    <table>
        <tr>
            <th>Chemin</th>
            <th>Résultat</th>
            <th>Image/Script</th>
        </tr>';

// Fonction pour tester un chemin
function testPath($path) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
    $exists = file_exists($fullPath);
    $isImage = strpos($path, '.png') !== false || strpos($path, '.jpg') !== false;
    
    echo '<tr>';
    echo '<td>' . htmlspecialchars($path) . '</td>';
    
    if ($exists) {
        echo '<td class="success">✅ Le fichier existe sur le serveur</td>';
    } else {
        echo '<td class="error">❌ Le fichier n\'existe pas sur le serveur</td>';
    }
    
    // Afficher l'image ou un lien vers le script
    echo '<td>';
    if ($isImage) {
        echo '<img src="' . htmlspecialchars($path) . '" style="max-height: 50px;" />';
    } else {
        echo '<a href="' . htmlspecialchars($path) . '" target="_blank">Voir le fichier</a>';
    }
    echo '</td>';
    
    echo '</tr>';
}

// Tester chaque chemin
foreach ($paths as $path) {
    testPath($path);
}

echo '</table>

<h2>Informations sur le serveur</h2>
<pre>';
echo 'DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo 'SCRIPT_FILENAME: ' . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo 'REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . "\n";
echo '</pre>

<h2>Test des images</h2>
<div>
    <h3>Test avec /images/logo1.png</h3>
    <img src="/images/logo1.png" style="max-height: 100px;" />
    
    <h3>Test avec /public/images/logo1.png</h3>
    <img src="/public/images/logo1.png" style="max-height: 100px;" />
</div>

<h2>Test des scripts</h2>
<div>
    <p>Vérifiez la console pour voir si les scripts sont chargés correctement</p>
    <script src="/js/tutorial.js"></script>
    <script>
        console.log("Test du chargement de /js/tutorial.js");
    </script>
    
    <script src="/public/js/tutorial.js"></script>
    <script>
        console.log("Test du chargement de /public/js/tutorial.js");
    </script>
</div>

</body>
</html>';
