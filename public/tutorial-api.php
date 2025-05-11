<?php
// Définition des headers pour l'API
header('Content-Type: application/json');

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Erreur de connexion à la base de données
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit;
}

// Récupérer les étapes du tutoriel
try {
    // Vérifier si la table existe
    $tableExists = false;
    $stmt = $pdo->query("SHOW TABLES LIKE 'tutorial_steps'");
    if ($stmt->rowCount() > 0) {
        $tableExists = true;
    }
    
    if ($tableExists) {
        // Récupérer les étapes du tutoriel depuis la base de données
        $stmt = $pdo->query('SELECT * FROM tutorial_steps ORDER BY step_order ASC');
        $steps = $stmt->fetchAll();
        
        // Formater les étapes pour IntroJS
        $tutorialSteps = [];
        foreach ($steps as $step) {
            $tutorialStep = [
                'title' => $step['title'],
                'intro' => $step['content'],
                'position' => $step['position']
            ];
            
            // Ajouter l'élément s'il est défini
            if (!empty($step['element_id'])) {
                $tutorialStep['element'] = $step['element_id'];
            }
            
            $tutorialSteps[] = $tutorialStep;
        }
        
        // Si aucune étape n'a été trouvée, utiliser les étapes par défaut
        if (empty($tutorialSteps)) {
            $tutorialSteps = getDefaultSteps();
        }
    } else {
        // Si la table n'existe pas, renvoyer les étapes par défaut
        $tutorialSteps = getDefaultSteps();
    }
    
    // Renvoyer les étapes au format JSON
    echo json_encode($tutorialSteps);
} catch (Exception $e) {
    // Erreur lors de la récupération des étapes
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des étapes du tutoriel: ' . $e->getMessage()]);
    exit;
}

// Fonction pour obtenir les étapes par défaut
function getDefaultSteps() {
    return [
        [
            'title' => '👋 Bienvenue sur TrucsPasChers!',
            'intro' => "Bienvenue dans ce tutoriel interactif! Nous allons vous guider à travers les fonctionnalités principales de notre plateforme pour que vous puissiez rapidement commencer à acheter et vendre des produits.",
            'position' => 'center'
        ],
        [
            'element' => '#tutorial-step-1',
            'title' => '🏠 Logo et Accueil',
            'intro' => "Voici notre logo <strong>TrucsPasChers</strong>! Cliquez dessus à tout moment pour revenir à la page d'accueil, où que vous soyez sur notre site.",
            'position' => 'bottom'
        ],
        [
            'element' => '#tutorial-step-2',
            'title' => '🧭 Menu de navigation',
            'intro' => "Utilisez ce menu pour naviguer facilement entre les différentes sections de notre site: <strong>produits, à propos de nous</strong> et <strong>contact</strong>. Explorez nos catégories pour trouver ce qui vous intéresse!",
            'position' => 'bottom'
        ],
        [
            'element' => '#tutorial-step-3',
            'title' => '🔍 Recherche rapide',
            'intro' => "Trouvez instantanément ce que vous cherchez! Utilisez cette barre pour <strong>rechercher des produits</strong> par mot-clé, catégorie ou description.",
            'position' => 'bottom'
        ],
        [
            'element' => '#tutorial-step-4',
            'title' => '🛒 Votre panier',
            'intro' => "C'est votre <strong>panier d'achat</strong>. Le nombre affiché indique combien d'articles s'y trouvent. Cliquez pour voir votre sélection, ajuster les quantités ou finaliser votre achat.",
            'position' => 'left'
        ],
        [
            'element' => '#tutorial-step-5',
            'title' => '🌓 Mode sombre',
            'intro' => "Préférez-vous un affichage clair ou sombre? Cliquez ici pour basculer entre les deux modes selon votre préférence visuelle ou l'heure de la journée.",
            'position' => 'left'
        ],
        [
            'title' => '💳 Comment acheter',
            'intro' => "<strong>Le processus d'achat est simple:</strong><br>1. Parcourez les produits ou utilisez la recherche<br>2. Cliquez sur un produit pour voir ses détails<br>3. Ajoutez-le à votre panier<br>4. Finalisez votre commande en vous connectant et en payant",
            'position' => 'bottom'
        ],
        [
            'title' => '📦 Comment vendre',
            'intro' => "<strong>Vous souhaitez vendre un produit?</strong><br>1. Connectez-vous à votre compte<br>2. Accédez à \"Ajouter un produit\" depuis votre menu profil<br>3. Remplissez les détails et ajoutez des photos<br>4. Publiez votre annonce et surveillez les demandes!",
            'position' => 'bottom'
        ],
        [
            'title' => '🎉 Vous êtes prêt!',
            'intro' => "Félicitations! Vous connaissez maintenant les bases pour naviguer, acheter et vendre sur TrucsPasChers. N'hésitez pas à lancer ce tutoriel à nouveau si besoin en cliquant sur le bouton d'aide en bas à droite de l'écran.",
            'position' => 'center'
        ]
    ];
}
