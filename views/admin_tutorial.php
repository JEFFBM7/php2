<?php
$title = 'Administration du tutoriel - TrucsPasChers';

// Vérifier si l'utilisateur est connecté et admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pour la démonstration, on vérifie simplement que l'utilisateur est connecté
// Dans une application réelle, vous ajouteriez une vérification du rôle admin
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Traitement du formulaire de mise à jour du tutoriel
$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupérer et valider les données
        $tutorialSteps = $_POST['tutorial_steps'] ?? [];
        
        // Connexion à la base de données
        $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        // Supprimer les étapes existantes
        $pdo->exec('TRUNCATE TABLE tutorial_steps');
        
        // Insérer les nouvelles étapes
        $stmt = $pdo->prepare('INSERT INTO tutorial_steps (step_order, element_id, title, content, position) VALUES (?, ?, ?, ?, ?)');
        
        foreach ($tutorialSteps as $index => $step) {
            $stmt->execute([
                $index + 1,
                $step['element_id'] ?? null,
                $step['title'] ?? '',
                $step['content'] ?? '',
                $step['position'] ?? 'bottom'
            ]);
        }
        
        $success = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupérer les étapes existantes du tutoriel
try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Récupérer les étapes du tutoriel
    $stmt = $pdo->query('SELECT * FROM tutorial_steps ORDER BY step_order ASC');
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si la table n'existe pas, on utilise les étapes par défaut
    $steps = [];
}
?>

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 max-w-4xl mx-auto my-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Administration du Tutoriel Interactif</h1>
        <a href="/" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors">
            Retour à l'accueil
        </a>
    </div>
    
    <?php if ($success): ?>
    <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded-lg mb-6">
        Le tutoriel a été mis à jour avec succès.
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 p-4 rounded-lg mb-6">
        Erreur: <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    
    <form method="post" action="" class="space-y-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Étapes du tutoriel</h2>
            <button type="button" id="add-step" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajouter une étape
            </button>
        </div>
        
        <div id="tutorial-steps" class="space-y-6">
            <!-- Les étapes du tutoriel seront ajoutées ici dynamiquement -->
            <?php if (empty($steps)): ?>
            <div class="step-container bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Étape 1: Introduction</h3>
                    <button type="button" class="remove-step text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            ID de l'élément (optionnel)
                        </label>
                        <input type="text" name="tutorial_steps[0][element_id]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white" placeholder="#tutorial-step-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Position
                        </label>
                        <select name="tutorial_steps[0][position]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                            <option value="bottom">Bas</option>
                            <option value="top">Haut</option>
                            <option value="left">Gauche</option>
                            <option value="right">Droite</option>
                            <option value="center">Centre</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Titre
                        </label>
                        <input type="text" name="tutorial_steps[0][title]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white" value="👋 Bienvenue sur TrucsPasChers!">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Contenu
                        </label>
                        <textarea name="tutorial_steps[0][content]" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">Bienvenue dans ce tutoriel interactif! Nous allons vous guider à travers les fonctionnalités principales de notre plateforme.</textarea>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($steps as $index => $step): ?>
                <div class="step-container bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Étape <?= $index + 1 ?></h3>
                        <button type="button" class="remove-step text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                ID de l'élément (optionnel)
                            </label>
                            <input type="text" name="tutorial_steps[<?= $index ?>][element_id]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white" value="<?= htmlspecialchars($step['element_id'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Position
                            </label>
                            <select name="tutorial_steps[<?= $index ?>][position]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                                <option value="bottom" <?= ($step['position'] ?? '') === 'bottom' ? 'selected' : '' ?>>Bas</option>
                                <option value="top" <?= ($step['position'] ?? '') === 'top' ? 'selected' : '' ?>>Haut</option>
                                <option value="left" <?= ($step['position'] ?? '') === 'left' ? 'selected' : '' ?>>Gauche</option>
                                <option value="right" <?= ($step['position'] ?? '') === 'right' ? 'selected' : '' ?>>Droite</option>
                                <option value="center" <?= ($step['position'] ?? '') === 'center' ? 'selected' : '' ?>>Centre</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Titre
                            </label>
                            <input type="text" name="tutorial_steps[<?= $index ?>][title]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white" value="<?= htmlspecialchars($step['title'] ?? '') ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Contenu
                            </label>
                            <textarea name="tutorial_steps[<?= $index ?>][content]" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"><?= htmlspecialchars($step['content'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="button" id="test-tutorial" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Tester le tutoriel
            </button>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tutorialStepsContainer = document.getElementById('tutorial-steps');
    const addStepButton = document.getElementById('add-step');
    const testTutorialButton = document.getElementById('test-tutorial');
    
    // Fonction pour ajouter une nouvelle étape
    addStepButton.addEventListener('click', function() {
        const stepIndex = document.querySelectorAll('.step-container').length;
        
        const stepContainer = document.createElement('div');
        stepContainer.className = 'step-container bg-gray-50 dark:bg-gray-700 p-4 rounded-lg';
        
        stepContainer.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Étape ${stepIndex + 1}</h3>
                <button type="button" class="remove-step text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        ID de l'élément (optionnel)
                    </label>
                    <input type="text" name="tutorial_steps[${stepIndex}][element_id]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white" placeholder="#tutorial-step-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Position
                    </label>
                    <select name="tutorial_steps[${stepIndex}][position]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                        <option value="bottom">Bas</option>
                        <option value="top">Haut</option>
                        <option value="left">Gauche</option>
                        <option value="right">Droite</option>
                        <option value="center">Centre</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Titre
                    </label>
                    <input type="text" name="tutorial_steps[${stepIndex}][title]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contenu
                    </label>
                    <textarea name="tutorial_steps[${stepIndex}][content]" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"></textarea>
                </div>
            </div>
        `;
        
        tutorialStepsContainer.appendChild(stepContainer);
        
        // Ajouter l'événement de suppression pour le nouveau bouton
        const removeButton = stepContainer.querySelector('.remove-step');
        removeButton.addEventListener('click', function() {
            stepContainer.remove();
            updateStepNumbers();
        });
    });
    
    // Ajouter les événements de suppression aux boutons existants
    document.querySelectorAll('.remove-step').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.step-container').remove();
            updateStepNumbers();
        });
    });
    
    // Mettre à jour la numérotation des étapes après une suppression
    function updateStepNumbers() {
        document.querySelectorAll('.step-container').forEach((container, index) => {
            // Mettre à jour le titre de l'étape
            container.querySelector('h3').textContent = `Étape ${index + 1}`;
            
            // Mettre à jour les noms des champs du formulaire
            container.querySelectorAll('input, textarea, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/tutorial_steps\[\d+\]/, `tutorial_steps[${index}]`));
                }
            });
        });
    }
    
    // Test du tutoriel
    testTutorialButton.addEventListener('click', function() {
        // Récupérer les données du formulaire
        const formData = new FormData(document.querySelector('form'));
        const tutorialSteps = [];
        
        // Pour chaque étape, créer un objet pour introJs
        for (let i = 0; i < document.querySelectorAll('.step-container').length; i++) {
            const step = {
                title: formData.get(`tutorial_steps[${i}][title]`) || '',
                intro: formData.get(`tutorial_steps[${i}][content]`) || '',
                position: formData.get(`tutorial_steps[${i}][position]`) || 'bottom'
            };
            
            // Ajouter l'élément s'il est spécifié
            const element = formData.get(`tutorial_steps[${i}][element_id]`);
            if (element) {
                step.element = element;
            }
            
            tutorialSteps.push(step);
        }
        
        // Créer et démarrer le tutoriel de test
        const intro = introJs();
        intro.setOptions({
            steps: tutorialSteps,
            nextLabel: 'Suivant &rarr;',
            prevLabel: '&larr; Précédent',
            doneLabel: 'Terminé',
            skipLabel: 'Passer',
            showBullets: true,
            showProgress: true,
            tooltipClass: 'customTooltip'
        });
        
        intro.start();
    });
});
</script>
<script src="/assets/js/admin-tutorial.js"></script>
