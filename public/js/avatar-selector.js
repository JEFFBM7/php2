// Script pour la sélection d'avatar
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les avatars
    const avatars = document.querySelectorAll('.avatar-option');
    const avatarInput = document.getElementById('avatar');
    
    // Ajouter un gestionnaire d'événements à chaque avatar
    avatars.forEach(avatar => {
        avatar.addEventListener('click', function() {
            // Retirer la classe active de tous les avatars
            avatars.forEach(a => a.classList.remove('active'));
            
            // Ajouter la classe active à l'avatar sélectionné
            this.classList.add('active');
            
            // Mettre à jour la valeur du champ caché
            avatarInput.value = this.dataset.avatar;
            
            // Si un avatar est sélectionné, désactiver l'option de téléchargement de photo de profil
            const photoProfileInput = document.getElementById('photo_profile');
            if (photoProfileInput) {
                photoProfileInput.value = '';
                
                // Désactiver visuellement la section de téléchargement de photo
                const photoSection = document.querySelector('.photo-upload-section');
                if (photoSection) {
                    photoSection.classList.add('opacity-50');
                }
            }
        });
    });
});
