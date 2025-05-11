// Script pour l'administration du tutoriel
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script d\'administration du tutoriel chargé');
    
    // Fonctionnalités pour l'interface d'administration du tutoriel
    const addStepButton = document.getElementById('add-step');
    const tutorialSteps = document.getElementById('tutorial-steps');
    const previewButton = document.getElementById('preview-tutorial');
    
    if (!addStepButton || !tutorialSteps) {
        console.warn('Éléments requis non trouvés');
        return;
    }
    
    // Ajouter une nouvelle étape
    addStepButton.addEventListener('click', function() {
        addNewStep();
    });
    
    // Fonctions pour gérer les boutons d'action des étapes
    function setupStepActions() {
        // Suppression d'étape
        document.querySelectorAll('.remove-step').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Voulez-vous vraiment supprimer cette étape?')) {
                    this.closest('.tutorial-step').remove();
                    updateStepOrder();
                }
            });
        });
        
        // Déplacer une étape vers le haut
        document.querySelectorAll('.move-step-up').forEach(button => {
            button.addEventListener('click', function() {
                const stepEl = this.closest('.tutorial-step');
                const prevEl = stepEl.previousElementSibling;
                
                if (prevEl && prevEl.classList.contains('tutorial-step')) {
                    tutorialSteps.insertBefore(stepEl, prevEl);
                    updateStepOrder();
                }
            });
        });
        
        // Déplacer une étape vers le bas
        document.querySelectorAll('.move-step-down').forEach(button => {
            button.addEventListener('click', function() {
                const stepEl = this.closest('.tutorial-step');
                const nextEl = stepEl.nextElementSibling;
                
                if (nextEl && nextEl.classList.contains('tutorial-step')) {
                    tutorialSteps.insertBefore(nextEl, stepEl);
                    updateStepOrder();
                }
            });
        });
        
        // Prévisualiser une étape
        document.querySelectorAll('.preview-step').forEach(button => {
            button.addEventListener('click', function() {
                const stepEl = this.closest('.tutorial-step');
                const title = stepEl.querySelector('input[name$="[title]"]').value;
                const content = stepEl.querySelector('textarea[name$="[content]"]').value;
                const elementId = stepEl.querySelector('input[name$="[element_id]"]').value;
                const position = Array.from(stepEl.querySelectorAll('input[name$="[position]"]'))
                    .find(input => input.checked)?.value || 'bottom';
                
                // Créer un intro.js avec une seule étape pour prévisualisation
                const intro = introJs();
                
                const step = {
                    title: title || 'Titre de l\'étape',
                    intro: content || 'Contenu de l\'étape',
                    position: position
                };
                
                if (elementId) {
                    step.element = elementId;
                }
                
                intro.setOptions({
                    steps: [step],
                    showBullets: false,
                    showProgress: true,
                    tooltipClass: 'customTooltip'
                });
                
                intro.start();
            });
        });
    }
    
    // Mettre à jour l'ordre des étapes
    function updateStepOrder() {
        const steps = document.querySelectorAll('.tutorial-step');
        steps.forEach((step, index) => {
            // Mettre à jour le numéro d'étape
            const stepNumber = step.querySelector('.step-number');
            if (stepNumber) stepNumber.textContent = index + 1;
            
            // Mettre à jour les attributs name des champs
            step.querySelectorAll('[name^="tutorial_steps["]').forEach(field => {
                const name = field.getAttribute('name');
                field.setAttribute('name', name.replace(/tutorial_steps\[\d+\]/, `tutorial_steps[${index}]`));
            });
        });
    }
    
    // Ajouter une nouvelle étape
    function addNewStep() {
        const stepCount = document.querySelectorAll('.tutorial-step').length;
        
        const newStep = document.createElement('div');
        newStep.className = 'tutorial-step bg-gray-50 dark:bg-gray-700 p-5 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm';
        
        newStep.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200 font-bold mr-3 step-number">
                        ${stepCount + 1}
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Étape ${stepCount + 1}</h3>
                </div>
                <div class="flex space-x-2">
                    <button type="button" class="move-step-up text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-1" title="Monter l'étape">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>
                    <button type="button" class="move-step-down text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-1" title="Descendre l'étape">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <button type="button" class="preview-step text-blue-500 hover:text-blue-700 dark:hover:text-blue-300 p-1" title="Prévisualiser l'étape">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    <button type="button" class="remove-step text-red-500 hover:text-red-700 dark:hover:text-red-300 p-1" title="Supprimer l'étape">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Titre
                    </label>
                    <input type="text" name="tutorial_steps[${stepCount}][title]"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white sm:text-sm"
                        placeholder="Ex: 👋 Bienvenue sur TrucsPasChers!" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Élément cible (sélecteur CSS)
                    </label>
                    <input type="text" name="tutorial_steps[${stepCount}][element_id]"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white sm:text-sm"
                        placeholder="Ex: #login-button ou .navbar (laisser vide pour message central)">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Contenu
                </label>
                <textarea name="tutorial_steps[${stepCount}][content]"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white sm:text-sm"
                    rows="3" required placeholder="Décrivez cette étape du tutoriel. Vous pouvez utiliser du HTML basique (<strong>, <br>, etc)."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Position
                </label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="tutorial_steps[${stepCount}][position]" value="top"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">Haut</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="tutorial_steps[${stepCount}][position]" value="bottom" checked
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">Bas</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="tutorial_steps[${stepCount}][position]" value="left"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">Gauche</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="tutorial_steps[${stepCount}][position]" value="right"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">Droite</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="tutorial_steps[${stepCount}][position]" value="center"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">Centre</span>
                    </label>
                </div>
            </div>
        `;
        
        tutorialSteps.appendChild(newStep);
        setupStepActions();
    }
    
    // Aperçu complet du tutoriel
    if (previewButton) {
        previewButton.addEventListener('click', function() {
            previewTutorial();
        });
    }
    
    function previewTutorial() {
        const steps = [];
        
        document.querySelectorAll('.tutorial-step').forEach((stepEl, index) => {
            const title = stepEl.querySelector('input[name$="[title]"]').value;
            const content = stepEl.querySelector('textarea[name$="[content]"]').value;
            const elementId = stepEl.querySelector('input[name$="[element_id]"]').value;
            const position = Array.from(stepEl.querySelectorAll('input[name$="[position]"]'))
                .find(input => input.checked)?.value || 'bottom';
            
            if (!title && !content) return;
            
            const step = {
                title: title || `Étape ${index + 1}`,
                intro: content || 'Contenu de l\'étape',
                position: position
            };
            
            if (elementId) {
                step.element = elementId;
            }
            
            steps.push(step);
        });
        
        if (steps.length === 0) {
            alert('Aucune étape à prévisualiser. Ajoutez au moins une étape.');
            return;
        }
        
        // Créer un intro.js avec toutes les étapes pour prévisualisation
        const intro = introJs();
        
        intro.setOptions({
            steps: steps,
            nextLabel: 'Suivant →',
            prevLabel: '← Précédent',
            doneLabel: 'Terminé',
            skipLabel: 'Passer',
            showBullets: true,
            showProgress: true,
            tooltipClass: 'customTooltip'
        });
        
        intro.start();
    }
    
    // Initialiser les actions sur les étapes existantes
    setupStepActions();
    
    // Si aucune étape n'est présente, ajouter une étape par défaut
    if (document.querySelectorAll('.tutorial-step').length === 0) {
        addNewStep();
    }
});
