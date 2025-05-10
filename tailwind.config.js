/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './views/**/*.php',
    './src/input.css', // Important pour que Tailwind traite les @apply dans ce fichier
    './public/**/*.php', // Au cas où des classes Tailwind seraient utilisées ici
    // Ajoutez d'autres chemins si vous utilisez des classes Tailwind dans des fichiers JS, etc.
    // './assets/js/**/*.js', 
  ],
  darkMode: 'class', // ou 'media', selon votre préférence pour le mode sombre
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#3490dc', // Un bleu par défaut
          light: '#6cb2eb',
          dark: '#2779bd',
        },
        secondary: {
          DEFAULT: '#ffed4a', // Un jaune par défaut
          light: '#fff382',
          dark: '#f9d923',
        }
        // La palette 'slate' devrait être disponible par défaut.
        // Si l'erreur persiste, nous pourrions avoir besoin de l'importer explicitement :
        // const colors = require('tailwindcss/colors');
        // ...
        // slate: colors.slate,
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', '"Noto Sans"', 'sans-serif', '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"'],
      },
      borderRadius: {
        'xl': '1rem', // 16px
        '2xl': '1.5rem', // 24px
        '3xl': '2rem', // 32px
      }
    },
  },
  plugins: [
    // require('@tailwindcss/forms'), // Si vous utilisez des formulaires stylisés par Tailwind
    // require('@tailwindcss/typography'), // Pour le style du contenu prose
  ],
}
