// Script pour la sélection d'avatar
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les avatars
    const avatars = document.querySelectorAll('.avatar-option');
    const previewImg = document.getElementById('avatar-preview');
    
    // Ajouter un gestionnaire d'événements à chaque avatar
    avatars.forEach(avatar => {
        avatar.addEventListener('click', function() {
            // Retirer la classe active de tous les avatars
            avatars.forEach(a => a.classList.remove('active'));
            
            // Ajouter la classe active à l'avatar sélectionné
            this.classList.add('active');
            
            // Cocher le radio input et mettre à jour l’aperçu
            const radio = this.querySelector('input[name="avatar"]');
            if (radio) {
                radio.checked = true;
                if (previewImg) {
                    previewImg.src = '/images/profile/avatars/' + radio.value;
                }
            }
            
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

    // Mettre à jour la prévisualisation lors du changement du radio input
    const avatarInputs = document.querySelectorAll('input[name="avatar"]');
    avatarInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked && previewImg) {
                previewImg.src = '/images/profile/avatars/' + this.value;
            }
        });
    });
});
