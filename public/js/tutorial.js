// Script pour le tutoriel interactif utilisant intro.js
document.addEventListener('DOMContentLoaded', function() {
    const startTutorialBtn = document.getElementById('start-tutorial-btn');
    
    if (startTutorialBtn) {
        startTutorialBtn.addEventListener('click', startTutorial);
    }
    
    function startTutorial() {
        // Configuration du tutoriel
        const intro = introJs();
        
        // Définir les étapes du tutoriel
        intro.setOptions({
            steps: [
                {
                    element: document.getElementById('tutorial-step-1'),
                    title: 'Bienvenue sur TrucsPasChers',
                    intro: 'Bienvenue dans notre plateforme ! Ce tutoriel vous guidera à travers les fonctionnalités principales.',
                    position: 'right'
                },
                {
                    element: document.getElementById('tutorial-step-2'),
                    title: 'Navigation',
                    intro: 'Utilisez ces liens pour naviguer entre les différentes sections du site.',
                    position: 'bottom'
                },
                {
                    element: document.getElementById('tutorial-step-3'),
                    title: 'Recherche',
                    intro: 'Recherchez rapidement des produits par nom, catégorie ou description.',
                    position: 'bottom'
                },
                {
                    element: document.getElementById('tutorial-step-4'),
                    title: 'Votre panier',
                    intro: 'Accédez à votre panier pour voir les articles que vous avez sélectionnés.',
                    position: 'left'
                },
                {
                    element: document.getElementById('tutorial-step-5'),
                    title: 'Mode sombre',
                    intro: 'Basculez entre le mode clair et sombre selon vos préférences.',
                    position: 'left'
                }
            ],
            tooltipClass: 'customTooltip',
            prevLabel: 'Précédent',
            nextLabel: 'Suivant',
            doneLabel: 'Terminer',
            showProgress: true
        });
        
        // Vérifier si l'utilisateur est connecté ou non pour afficher l'étape correspondante
        const authElement = document.getElementById('tutorial-step-6-auth');
        const noAuthElement = document.getElementById('tutorial-step-6-noauth');
        
        if (authElement) {
            intro.addStep({
                element: authElement,
                title: 'Votre compte',
                intro: 'Accédez à votre profil, gérez vos produits, ou déconnectez-vous.',
                position: 'left'
            });
        } else if (noAuthElement) {
            intro.addStep({
                element: noAuthElement,
                title: 'Connexion / Inscription',
                intro: 'Connectez-vous à votre compte ou créez-en un nouveau pour profiter de toutes les fonctionnalités !',
                position: 'left'
            });
        }
        
        // Démarrer le tutoriel
        intro.start();
        
        // Sauvegarder la préférence utilisateur après avoir terminé le tutoriel
        intro.oncomplete(function() {
            saveTutorialPreference('completed');
        });
        
        intro.onexit(function() {
            saveTutorialPreference('dismissed');
        });
    }
    
    function saveTutorialPreference(status) {
        // Enregistrer la préférence via une requête AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/save-tutorial-preference.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('status=' + status);
    }
});
