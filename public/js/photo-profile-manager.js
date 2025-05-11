// Script pour gérer la sélection de l'avatar et le téléchargement de photo
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du mode de sélection (avatar prédéfini ou photo personnalisée)
    const useAvatarRadio = document.getElementById('use-avatar');
    const useCustomPhotoRadio = document.getElementById('use-custom-photo');
    const avatarsSection = document.getElementById('avatars-section');
    const customPhotoSection = document.getElementById('custom-photo-section');
    const useCustomPhotoField = document.getElementById('use_custom_photo');
    
    if (useAvatarRadio && useCustomPhotoRadio) {
        useAvatarRadio.addEventListener('change', function() {
            if (this.checked) {
                avatarsSection.classList.remove('hidden');
                customPhotoSection.classList.add('hidden');
                useCustomPhotoField.value = '0';
            }
        });
        
        useCustomPhotoRadio.addEventListener('change', function() {
            if (this.checked) {
                avatarsSection.classList.add('hidden');
                customPhotoSection.classList.remove('hidden');
                useCustomPhotoField.value = '1';
            }
        });
    }
    
    // Prévisualisation de la photo téléchargée
    const customPhotoInput = document.getElementById('custom_photo');
    if (customPhotoInput) {
        customPhotoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const currentPhotoPreview = document.getElementById('current-photo-preview');
                    
                    if (currentPhotoPreview) {
                        currentPhotoPreview.src = e.target.result;
                    } else {
                        // Créer un élément de prévisualisation s'il n'existe pas
                        const previewContainer = document.querySelector('#custom-photo-section .flex-col');
                        
                        if (previewContainer) {
                            const textElement = document.createElement('div');
                            textElement.className = 'text-sm text-gray-600 dark:text-gray-400 mb-2';
                            textElement.textContent = 'Aperçu de votre photo';
                            
                            const imageContainer = document.createElement('div');
                            imageContainer.className = 'w-32 h-32 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden';
                            
                            const imageElement = document.createElement('img');
                            imageElement.src = e.target.result;
                            imageElement.alt = 'Aperçu de la photo';
                            imageElement.className = 'w-full h-full object-cover';
                            imageElement.id = 'current-photo-preview';
                            
                            imageContainer.appendChild(imageElement);
                            previewContainer.appendChild(textElement);
                            previewContainer.appendChild(imageContainer);
                        }
                    }
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Réinitialiser les sélections lors de la soumission du formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            if (useCustomPhotoRadio.checked && !customPhotoInput.files.length) {
                // Si l'utilisateur veut utiliser une photo personnalisée mais n'en a pas fourni
                if (!document.getElementById('current-photo-preview')) {
                    // Et s'il n'y a pas déjà une photo personnalisée
                    event.preventDefault();
                    alert('Veuillez sélectionner une photo ou choisir un avatar prédéfini.');
                }
            }
        });
    }
});
