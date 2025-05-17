// Script pour la gestion de photo de profil
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('custom_photo');
    const photoPreview = document.getElementById('current-photo-preview');
    const avatarsSection = document.getElementById('avatars-section');
    const customPhotoSection = document.getElementById('custom-photo-section');
    const useAvatarRadio = document.getElementById('use-avatar');
    const useCustomRadio = document.getElementById('use-custom-photo');
    const hiddenUseCustom = document.getElementById('use_custom_photo');

    // Initialiser l'affichage des sections selon la sélection actuelle
    if (useAvatarRadio && useCustomRadio) {
        function toggleSections() {
            if (useCustomRadio.checked) {
                avatarsSection.classList.add('hidden');
                customPhotoSection.classList.remove('hidden');
                if (hiddenUseCustom) hiddenUseCustom.value = '1';
            } else {
                avatarsSection.classList.remove('hidden');
                customPhotoSection.classList.add('hidden');
                if (hiddenUseCustom) hiddenUseCustom.value = '0';
            }
        }
        // Exécution initiale
        toggleSections();
        // Écouteurs sur click pour garantir le basculement
        useAvatarRadio.addEventListener('click', toggleSections);
        useCustomRadio.addEventListener('click', toggleSections);
        // Écouteurs sur change pour garantir le basculement
        useAvatarRadio.addEventListener('change', toggleSections);
        useCustomRadio.addEventListener('change', toggleSections);
    }

    // Basculer l’affichage des sections selon la sélection
    if (useAvatarRadio) {
        useAvatarRadio.addEventListener('change', function() {
            avatarsSection.classList.remove('hidden');
            customPhotoSection.classList.add('hidden');
            if (hiddenUseCustom) hiddenUseCustom.value = '0';
        });
    }
    if (useCustomRadio) {
        useCustomRadio.addEventListener('change', function() {
            avatarsSection.classList.add('hidden');
            customPhotoSection.classList.remove('hidden');
            if (hiddenUseCustom) hiddenUseCustom.value = '1';
        });
    }

    // Aperçu de l’image téléchargée
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            // Passer automatiquement en mode photo personnalisée
            if (useCustomRadio) useCustomRadio.checked = true;
            if (hiddenUseCustom) hiddenUseCustom.value = '1';
            avatarsSection.classList.add('hidden');
            customPhotoSection.classList.remove('hidden');
            if (this.files && this.files[0] && photoPreview) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Si le bouton de réinitialisation existe
    const resetPhotoBtn = document.getElementById('reset-photo');
    if (resetPhotoBtn) {
        resetPhotoBtn.addEventListener('click', function() {
            // Réinitialiser l'input de fichier
            photoInput.value = '';
            
            // Masquer l'aperçu
            photoPreview.src = '';
            
            // Réactiver la section des avatars
            if (avatarsSection) {
                avatarsSection.classList.remove('hidden');
            }
            if (customPhotoSection) {
                customPhotoSection.classList.add('hidden');
            }
            if (hiddenUseCustom) hiddenUseCustom.value = '0';
        });
    }

    // Toggle section mot de passe
    const togglePassword = document.getElementById('toggle-password');
    const passwordSection = document.getElementById('password-section');
    if (togglePassword && passwordSection) {
        togglePassword.addEventListener('change', function() {
            if (this.checked) {
                passwordSection.classList.remove('hidden');
            } else {
                passwordSection.classList.add('hidden');
                // Réinitialiser les champs si décoché
                const pwd = document.getElementById('password');
                const pwdConfirm = document.getElementById('password_confirm');
                if (pwd) pwd.value = '';
                if (pwdConfirm) pwdConfirm.value = '';
            }
        });
    }
});
