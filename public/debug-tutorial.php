<?php
// Ce fichier est destiné à déboguer le fonctionnement de l'API du tutoriel

// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Débogage du tutoriel interactif</h1>";

// Test 1: Vérifier si le fichier API est accessible
echo "<h2>Test 1: Vérifier l'accès au fichier API</h2>";
if (file_exists(__DIR__ . '/tutorial-api.php')) {
    echo "<p style='color: green;'>✅ Le fichier tutorial-api.php existe.</p>";
} else {
    echo "<p style='color: red;'>❌ Le fichier tutorial-api.php n'existe pas.</p>";
}

// Test 2: Vérifier la connexion à la base de données
echo "<h2>Test 2: Vérifier la connexion à la base de données</h2>";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "<p style='color: green;'>✅ Connexion à la base de données réussie.</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion à la base de données: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 3: Vérifier l'existence de la table tutorial_steps
echo "<h2>Test 3: Vérifier l'existence de la table tutorial_steps</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'tutorial_steps'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ La table tutorial_steps existe.</p>";
        
        // Vérifier les données dans la table
        $stmt = $pdo->query('SELECT COUNT(*) FROM tutorial_steps');
        $count = $stmt->fetchColumn();
        echo "<p>Nombre d'étapes dans la table: " . $count . "</p>";
        
        if ($count > 0) {
            echo "<h3>Contenu de la table tutorial_steps:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Ordre</th><th>Élément</th><th>Titre</th><th>Contenu</th><th>Position</th></tr>";
            
            $stmt = $pdo->query('SELECT * FROM tutorial_steps ORDER BY step_order ASC');
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['step_order']) . "</td>";
                echo "<td>" . htmlspecialchars($row['element_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($row['content'], 0, 50)) . "...</td>";
                echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ La table existe mais ne contient aucune donnée.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ La table tutorial_steps n'existe pas.</p>";
        
        // Afficher le SQL pour créer la table
        echo "<h3>SQL pour créer la table:</h3>";
        echo "<pre>
CREATE TABLE IF NOT EXISTS tutorial_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    step_order INT NOT NULL,
    element_id VARCHAR(50) NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    position VARCHAR(20) NOT NULL DEFAULT 'bottom',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
        </pre>";
        
        // Proposer de créer la table
        echo "<form method='post'>";
        echo "<input type='hidden' name='create_table' value='1'>";
        echo "<button type='submit' style='padding: 5px 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Créer la table tutorial_steps</button>";
        echo "</form>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erreur lors de la vérification de la table: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Créer la table si demandé
if (isset($_POST['create_table'])) {
    try {
        $pdo->exec('
        CREATE TABLE IF NOT EXISTS tutorial_steps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            step_order INT NOT NULL,
            element_id VARCHAR(50) NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            position VARCHAR(20) NOT NULL DEFAULT \'bottom\',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');
        echo "<p style='color: green;'>✅ Table tutorial_steps créée avec succès. Rechargez la page.</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Erreur lors de la création de la table: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Test 4: Simuler un appel à l'API
echo "<h2>Test 4: Simuler un appel à l'API</h2>";
$url = 'http://' . $_SERVER['HTTP_HOST'] . '/tutorial-api';
echo "<p>URL de l'API: <code>" . htmlspecialchars($url) . "</code></p>";

// Tester l'accès à l'API avec file_get_contents
echo "<h3>Résultat de l'appel à l'API:</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow: auto;'>";
try {
    $context = stream_context_create([
        'http' => ['ignore_errors' => true]
    ]);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "<p style='color: red;'>❌ Erreur lors de l'accès à l'API.</p>";
    } else {
        $httpStatus = $http_response_header[0];
        echo "<p>Statut HTTP: " . htmlspecialchars($httpStatus) . "</p>";
        
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
        
        // Vérifier si la réponse est du JSON valide
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "<p style='color: green;'>✅ Réponse JSON valide.</p>";
            echo "<p>Nombre d'étapes renvoyées: " . count($data) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ La réponse n'est pas du JSON valide: " . json_last_error_msg() . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception lors de l'accès à l'API: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// Test 5: Vérifier le chargement des fichiers JavaScript
echo "<h2>Test 5: Vérifier les ressources JavaScript</h2>";
if (file_exists(__DIR__ . '/../assets/js/tutorial.js')) {
    echo "<p style='color: green;'>✅ Le fichier tutorial.js existe.</p>";
    
    // Afficher le contenu du début du fichier
    echo "<h3>Début du fichier tutorial.js:</h3>";
    echo "<pre>" . htmlspecialchars(substr(file_get_contents(__DIR__ . '/../assets/js/tutorial.js'), 0, 500)) . "...</pre>";
} else {
    echo "<p style='color: red;'>❌ Le fichier tutorial.js n'existe pas ou n'est pas accessible.</p>";
}

// Instructions de débogage côté client
echo "<h2>Instructions pour le débogage dans le navigateur</h2>";
echo "<ol>";
echo "<li>Ouvrez la console développeur de votre navigateur (F12 ou Ctrl+Shift+I)</li>";
echo "<li>Naviguez vers l'onglet 'Console' pour voir les messages d'erreur JavaScript</li>";
echo "<li>Naviguez vers l'onglet 'Réseau' et recherchez l'appel à 'tutorial-api' pour vérifier sa réponse</li>";
echo "<li>Vérifiez que les scripts intro.js et tutorial.js sont bien chargés dans l'onglet 'Sources'</li>";
echo "</ol>";

// Proposer un correctif pour le problème courant
echo "<h2>Solutions possibles</h2>";
echo "<ol>";
echo "<li>Si la table n'existe pas, cliquez sur le bouton ci-dessus pour la créer</li>";
echo "<li>Assurez-vous que les scripts intro.js et tutorial.js sont correctement inclus dans la page</li>";
echo "<li>Vérifiez que le bouton avec l'ID 'start-tutorial-btn' est présent dans la page</li>";
echo "<li>Essayez de forcer le lancement du tutoriel en ajoutant <code>?show_tutorial=1</code> à l'URL de la page d'accueil</li>";
echo "<li>Videz le cache de votre navigateur ou essayez avec un mode de navigation privée</li>";
echo "</ol>";

// Bouton pour tester manuellement le lancement du tutoriel
echo "<h2>Test manuel du tutoriel</h2>";
echo "<button onclick=\"if (typeof introJs === 'function') { introJs().start(); } else { alert('La bibliothèque Intro.js n\\'est pas chargée!'); }\" style='padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Lancer un tutoriel basique</button>";

// Ajouter un script de débogage
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Débogage du tutoriel:');
    console.log('- Le script de débogage est chargé');
    
    // Vérifier si Intro.js est chargé
    if (typeof introJs === 'function') {
        console.log('- Intro.js est correctement chargé');
    } else {
        console.error('- Intro.js n\\'est PAS chargé!');
    }
    
    // Vérifier si le bouton de tutoriel existe
    const tutorialBtn = document.getElementById('start-tutorial-btn');
    if (tutorialBtn) {
        console.log('- Le bouton de tutoriel existe dans le DOM');
    } else {
        console.error('- Le bouton de tutoriel n\\'existe PAS dans le DOM!');
    }
    
    // Vérifier les éléments du tutoriel
    const checkElement = (id) => {
        const el = document.getElementById(id);
        if (el) {
            console.log(`- L'élément #${id} existe`);
        } else {
            console.warn(`- L'élément #${id} n'existe PAS`);
        }
    };
    
    checkElement('tutorial-step-1');
    checkElement('tutorial-step-2');
    checkElement('tutorial-step-3');
    checkElement('tutorial-step-4');
    checkElement('tutorial-step-5');
    checkElement('tutorial-step-6-auth');
    checkElement('tutorial-step-6-noauth');
    checkElement('tutorial-step-7');
});
</script>";
?>
