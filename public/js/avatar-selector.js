// Script pour améliorer la sélection d'avatar
document.addEventListener('DOMContentLoaded', function() {
  const avatarOptions = document.querySelectorAll('.avatar-option input');
  
  avatarOptions.forEach(option => {
    option.addEventListener('change', function() {
      // Animation lorsqu'un avatar est sélectionné
      const selectedLabel = this.nextElementSibling;
      selectedLabel.classList.add('animate-bounce');
      setTimeout(() => {
        selectedLabel.classList.remove('animate-bounce');
      }, 500);
      
      // Mettre à jour la prévisualisation de l'avatar sélectionné
      const previewImg = document.getElementById('avatar-preview');
      if (previewImg) {
        previewImg.src = `/public/images/profile/avatars/${this.value}`;
        
        // Animation de l'aperçu
        previewImg.classList.add('animate-pulse');
        setTimeout(() => {
          previewImg.classList.remove('animate-pulse');
        }, 1000);
      }
    });
  });
});
