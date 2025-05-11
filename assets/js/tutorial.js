/**
 * Script de tutoriel interactif pour TrucsPasChers
 * Version simplifiée et corrigée
 */
document.addEventListener('DOMContentLoaded', function () {
    console.log('Script de tutoriel chargé');
    const startTutorialButton = document.getElementById('start-tutorial-btn');
    
    // Si le bouton n'existe pas, on arrête ici
    if (!startTutorialButton) {
        console.warn('Bouton de tutoriel non trouvé dans le DOM');
        return;
    }
    
    console.log('Bouton de tutoriel trouvé, ID:', startTutorialButton.id);
    
    // Étapes de base du tutoriel (sans dépendance à l'API)
    const tutorialSteps = [
        {
            title: '👋 Bienvenue sur TrucsPasChers!',
            intro: "Bienvenue dans ce tutoriel interactif! Nous allons vous guider à travers les fonctionnalités principales de notre plateforme.",
            position: 'center'
        },
        {
            element: '#tutorial-step-1',
            title: '🏠 Logo et Accueil',
            intro: "Voici notre logo <strong>TrucsPasChers</strong>! Cliquez dessus pour revenir à l'accueil à tout moment.",
            position: 'bottom'
        },
        {
            element: '#tutorial-step-2',
            title: '🧭 Menu de navigation',
            intro: "Utilisez ce menu pour naviguer facilement entre les différentes sections de notre site.",
            position: 'bottom'
        },
        {
            element: '#tutorial-step-3',
            title: '🔍 Recherche',
            intro: "Utilisez cette barre pour <strong>rechercher des produits</strong>.",
            position: 'bottom'
        },
        {
            element: '#tutorial-step-4',
            title: '🛒 Votre panier',
            intro: "C'est votre <strong>panier d'achat</strong>. Le nombre affiché indique combien d'articles s'y trouvent.",
            position: 'left'
        },
        {
            title: '🎉 Vous êtes prêt!',
            intro: "Félicitations! Vous connaissez maintenant les bases pour utiliser notre site.",
            position: 'center'
        }
    ];
    
    // Fonction principale pour lancer le tutoriel
    function startTutorial() {
        console.log('Lancement du tutoriel');
        
        try {
            // Vérifier si Intro.js est disponible
            if (typeof introJs !== 'function') {
                console.error('La bibliothèque Intro.js n\'est pas chargée');
                alert('Erreur: La bibliothèque Intro.js n\'est pas chargée');
                return;
            }
            
            // Créer et configurer le tutoriel avec les étapes par défaut
            const intro = introJs();
            
            intro.setOptions({
                steps: tutorialSteps,
                nextLabel: 'Suivant →',
                prevLabel: '← Précédent',
                doneLabel: 'Terminé',
                skipLabel: 'Passer',
                showBullets: true,
                showProgress: true,
                tooltipClass: 'customTooltip'
            });
            
            // Événement à la fin du tutoriel
            intro.oncomplete(function() {
                console.log('Tutoriel terminé');
                localStorage.setItem('tutorialSeen', 'true');
                localStorage.setItem('tutorialCompleted', 'true');
                showTutorialCompleteFeedback();
            });
            
            // Événement si l'utilisateur quitte le tutoriel
            intro.onexit(function() {
                console.log('Tutoriel quitté');
                localStorage.setItem('tutorialSeen', 'true');
            });
            
            // Lancer le tutoriel
            intro.start();
            console.log('Tutoriel démarré avec succès');
            
        } catch (error) {
            console.error('Erreur lors du lancement du tutoriel:', error);
            alert('Une erreur s\'est produite lors du lancement du tutoriel');
        }
    }
    
    // Attacher l'événement de clic au bouton de tutoriel
    startTutorialButton.addEventListener('click', startTutorial);
    console.log('Événement de clic ajouté au bouton de tutoriel');
    
    // Fonction pour afficher un message de félicitation après avoir terminé le tutoriel
    function showTutorialCompleteFeedback() {
        // Créer un élément de notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-8 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white px-7 py-4 rounded-xl shadow-2xl z-50 flex items-center border border-indigo-300';
        notification.style.transition = 'all 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55)';
        notification.style.opacity = '0';
        notification.style.transform = 'translate(-50%, -20px)';
        notification.style.maxWidth = '450px';
        
        // Contenu de la notification avec un style amélioré
        notification.innerHTML = `
            <div class="p-2 bg-white bg-opacity-20 rounded-full mr-4 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-1">Félicitations !</h4>
                <p>Vous avez terminé le tutoriel avec succès ! Profitez maintenant de TrucsPasChers.</p>
            </div>
            <button class="ml-4 text-white hover:text-gray-200 focus:outline-none" onclick="this.parentNode.remove();" aria-label="Fermer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        // Ajouter à la page
        document.body.appendChild(notification);
        
        // Animer l'apparition avec un effet de rebond
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translate(-50%, 0)';
        }, 100);
        
        // Ajouter un effet de survol
        notification.addEventListener('mouseenter', () => {
            notification.style.boxShadow = '0 20px 25px -5px rgba(79, 70, 229, 0.4), 0 10px 10px -5px rgba(79, 70, 229, 0.3)';
        });
        
        notification.addEventListener('mouseleave', () => {
            notification.style.boxShadow = '';
        });
        
        // Supprimer après 7 secondes (un peu plus long pour permettre à l'utilisateur de lire)
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translate(-50%, -20px)';
            setTimeout(() => {
                notification.remove();
            }, 600);
        }, 7000);
    }
});