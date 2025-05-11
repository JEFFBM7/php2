<?php
// Fichier pour déboguer les problèmes de chemins de fichiers JavaScript

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Débogage des chemins de script</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2 { color: #4338ca; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Débogage des chemins de script</h1>
    
    <div id="results">
        <h2>Inspection des scripts</h2>
        <p>Cette page analyse les scripts chargés et détecte les chemins utilisés.</p>
    </div>

    <!-- Script qui inspecte les scripts chargés sur la page -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resultDiv = document.getElementById('results');
            
            // Récupérer tous les éléments script
            const scripts = document.querySelectorAll('script');
            
            resultDiv.innerHTML += '<h3>Scripts détectés sur la page:</h3>';
            resultDiv.innerHTML += '<pre>';
            
            if (scripts.length === 0) {
                resultDiv.innerHTML += 'Aucun script détecté.\n';
            } else {
                scripts.forEach((script, index) => {
                    resultDiv.innerHTML += `[${index}] ${script.src || '(inline script)'}\n`;
                });
            }
            
            resultDiv.innerHTML += '</pre>';
            
            // Tester si certains scripts causent des erreurs
            resultDiv.innerHTML += '<h3>Test de chargement des scripts:</h3>';
            resultDiv.innerHTML += '<ul>';
            
            // Tester chaque URL potentielle
            testScript('/assets/js/tutorial.js');
            testScript('/js/tutorial.js');
            
            resultDiv.innerHTML += '</ul>';
            
            // Ajouter des informations sur la page actuelle
            resultDiv.innerHTML += '<h3>Informations sur la page:</h3>';
            resultDiv.innerHTML += '<pre>';
            resultDiv.innerHTML += `URL: ${window.location.href}\n`;
            resultDiv.innerHTML += `Path: ${window.location.pathname}\n`;
            resultDiv.innerHTML += `Document referrer: ${document.referrer}\n`;
            resultDiv.innerHTML += '</pre>';
            
            function testScript(url) {
                const script = document.createElement('script');
                script.src = url;
                script.onload = function() {
                    resultDiv.innerHTML += `<li class="success">✅ Script chargé: ${url}</li>`;
                };
                script.onerror = function() {
                    resultDiv.innerHTML += `<li class="error">❌ Erreur de chargement: ${url}</li>`;
                };
                document.head.appendChild(script);
            }
        });
    </script>
    
    <!-- Chargement des scripts pour voir lesquels fonctionnent -->
    <script>
        function loadScript(url) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = url;
                script.onload = () => resolve(url);
                script.onerror = () => reject(new Error(`Échec du chargement de ${url}`));
                document.head.appendChild(script);
            });
        }
        
        // Tester si Intro.js peut être chargé
        loadScript('https://unpkg.com/intro.js/minified/intro.min.js')
            .then(url => console.log('Intro.js chargé avec succès'))
            .catch(error => console.error('Erreur de chargement d\'Intro.js:', error));
    </script>
</body>
</html>
