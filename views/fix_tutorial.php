<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lancement du tutoriel</title>
    <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #4338ca;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        button {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 0;
        }
        button:hover {
            background-color: #4338ca;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .info {
            background-color: #e0f2fe;
            border-left: 4px solid #38bdf8;
            padding: 10px;
            margin: 10px 0;
        }
        code {
            background-color: #f1f5f9;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <h1>Lancement du tutoriel interactif</h1>
    
    <div class="card">
        <h2>Problème détecté</h2>
        <p>Votre tutoriel interactif ne se lance pas correctement. Cette page va vous aider à identifier et résoudre le problème.</p>
    </div>
    
    <div class="info">
        <p><strong>Remarque :</strong> Cette page est un correctif temporaire pour lancer manuellement le tutoriel. Une fois le problème résolu, vous pourrez utiliser normalement le bouton de tutoriel sur votre site.</p>
    </div>
    
    <h2>Solutions possibles</h2>
    
    <h3>1. Lancer manuellement le tutoriel</h3>
    <p>Cliquez sur le bouton ci-dessous pour lancer manuellement le tutoriel avec les étapes par défaut :</p>
    <button id="start-tutorial">Lancer le tutoriel</button>
    
    <h3>2. Vérifier la table dans la base de données</h3>
    <p>Assurez-vous que la table <code>tutorial_steps</code> existe dans votre base de données :</p>
    <button onclick="window.location.href='/debug-tutorial'">Diagnostiquer le problème</button>
    
    <h3>3. Résoudre les problèmes dans le fichier tutorial.js</h3>
    <p>Voici un extrait du code qui pourrait poser problème dans votre fichier tutorial.js :</p>
    <pre style="background-color: #f1f5f9; padding: 10px; overflow-x: auto; border-radius: 5px;"><code>// Problème potentiel : virgule après "steps: steps" créant une erreur de syntaxe
intro.setOptions({
    steps: steps, // <= Vérifiez qu'il n'y a pas d'espace après la virgule
    nextLabel: 'Suivant &rarr;',
    // ...
});</code></pre>
    
    <h3>4. Revenir à la page d'accueil avec le paramètre de forçage</h3>
    <p>Ajoutez <code>?show_tutorial=1</code> à l'URL de la page d'accueil pour forcer le lancement du tutoriel :</p>
    <button onclick="window.location.href='/?show_tutorial=1'">Forcer le lancement du tutoriel</button>
    
    <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>
    <script>
        document.getElementById('start-tutorial').addEventListener('click', function() {
            // Tutoriel de base avec des étapes par défaut
            const intro = introJs();
            intro.setOptions({
                steps: [
                    {
                        title: '👋 Bienvenue sur TrucsPasChers!',
                        intro: "Bienvenue dans ce tutoriel interactif de dépannage! Nous allons vérifier si Intro.js fonctionne correctement.",
                        position: 'center'
                    },
                    {
                        title: '🔍 Vérification du problème',
                        intro: "Si vous voyez cette étape, cela signifie qu'Intro.js fonctionne correctement. Le problème vient probablement de la configuration des étapes ou de l'API.",
                        position: 'bottom'
                    },
                    {
                        title: '🛠️ Solution',
                        intro: "Consultez la page de diagnostic <code>/debug-tutorial</code> pour plus d'informations sur le problème et les solutions possibles.",
                        position: 'bottom'
                    },
                    {
                        title: '🎉 Prochaines étapes',
                        intro: "Rendez-vous sur <code>/admin-tutorial</code> pour gérer les étapes de votre tutoriel, ou revenez à la page d'accueil avec <code>?show_tutorial=1</code> pour tester le tutoriel complet.",
                        position: 'center'
                    }
                ],
                nextLabel: 'Suivant &rarr;',
                prevLabel: '&larr; Précédent',
                doneLabel: 'Terminé',
                skipLabel: 'Passer',
                showBullets: true,
                showProgress: true
            });
            
            intro.start();
        });
        
        // Console de débogage
        console.log("Vérification d'Intro.js:", typeof introJs === 'function' ? "Chargé" : "Non chargé");
    </script>
</body>
</html>
