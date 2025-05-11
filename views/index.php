<?php
$title = 'TrucsPasChers - Accueil';
require_once __DIR__ . '/../vendor/autoload.php';
use App\Model\Etudiant;

// Démarrer ou récupérer la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'étudiant connecté si disponible
$etudiant = null;
if (!empty($_SESSION['user_id'])) {
    $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $etudiant = $stmt->fetchObject(Etudiant::class);
    
    // Stocker l'objet étudiant dans la session pour y accéder facilement
    $_SESSION['student'] = $etudiant;
}
?>
<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 min-h-screen">
    <div class="py-12 px-4 mx-auto max-w-screen-xl text-center lg:py-20 lg:px-12 relative overflow-hidden">
        <!-- Badge de notification avec animation améliorée -->
        <a href="#produits"
            class="inline-flex justify-between items-center py-1 px-1 pr-4 mb-8 text-sm text-gray-700 bg-gray-100 rounded-full dark:bg-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 shadow-md hover:shadow-lg"
            role="alert">
            <span class="text-xs bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-full text-white px-4 py-1.5 mr-3 shadow-inner">Nouveau</span>
            <span class="text-sm font-medium">Découvrez nos derniers produits</span>
            <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"></path>
            </svg>
        </a>

        <!-- Titre principal avec animation au chargement -->
        <h1 class="mb-6 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
            Trouvez des produits de qualité à prix <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700">imbattables</span>
        </h1>

        <p class="mb-8 text-lg font-normal text-gray-600 lg:text-xl sm:px-16 xl:px-48 dark:text-gray-300">
            Sur TrucsPasChers, nous proposons une sélection des meilleurs produits au meilleur prix, garantis par notre communauté d'experts.
        </p>

        <!-- Boutons d'action avec effet hover amélioré -->
        <div class="flex flex-col mb-12 lg:mb-16 space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
            <a href="#produits"
                class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800 focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-900 transition-all duration-300 shadow-md hover:shadow-xl">
                Explorer les produits
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
            <a href="/contact"
                class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800 transition-all duration-300 shadow-sm hover:shadow-md">
                <svg class="mr-2 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                </svg>
                Nous contacter
            </a>
        </div>
        
        <!-- Éléments décoratifs -->
        <div class="absolute top-0 right-0 hidden lg:block opacity-10 dark:opacity-5">
            <svg width="400" height="400" viewBox="0 0 200 200">
                <path fill="currentColor" d="M46,-78.1C61.3,-71.3,76.4,-61.5,86.6,-47.4C96.8,-33.2,102.1,-14.8,98.9,1.9C95.7,18.5,84,33.3,71.8,45.9C59.7,58.5,47,68.9,33.1,76.1C19.1,83.3,3.8,87.3,-13.7,86.8C-31.3,86.3,-51.1,81.2,-65.2,69.8C-79.2,58.4,-87.5,40.7,-87.8,23.8C-88.1,6.9,-80.3,-9.3,-72.3,-24.1C-64.3,-38.8,-56,-52.2,-44.2,-60.5C-32.3,-68.8,-16.2,-72.1,-0.4,-71.4C15.4,-70.7,30.8,-65.9,46,-78.1Z" transform="translate(100 100)" />
            </svg>
        </div>
    </div>
</section>

<!-- Section des produits -->
<section id="produits" class="bg-gray-50 py-12 antialiased dark:bg-gray-900 md:py-16">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-8 2xl:px-0">
        <!-- Affichage de la photo de profil de l'utilisateur connecté -->
        <?php if (!empty($_SESSION['student'])): ?>
        <div class="flex items-center justify-end mb-4">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Bonjour, <?= htmlspecialchars($_SESSION['student']->getNom()) ?>
                </span>
                <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-indigo-500 shadow-md">
                    <?php if (!empty($_SESSION['student']->getPhotoProfile())): ?>
                        <img src="/public/images/profile/uploads/<?= htmlspecialchars($_SESSION['student']->getPhotoProfile()) ?>" 
                             alt="Photo de profil de <?= htmlspecialchars($_SESSION['student']->getNom()) ?>" 
                             class="w-full h-full object-cover">
                    <?php elseif (!empty($_SESSION['student']->getAvatar())): ?>
                        <img src="/public/images/profile/avatars/<?= htmlspecialchars($_SESSION['student']->getAvatar()) ?>" 
                             alt="Avatar de <?= htmlspecialchars($_SESSION['student']->getNom()) ?>" 
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <img src="/public/images/default.png" 
                             alt="Avatar par défaut" 
                             class="w-full h-full object-cover">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Titre de section avec sous-titre -->
        <div class="mb-10 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl mb-2">Nos produits populaires</h2>
            <p class="text-gray-600 dark:text-gray-400">Découvrez notre sélection de produits tendance</p>
        </div>

        <!-- Filtres et catégories -->
        <div class="mb-8 flex flex-wrap gap-4 justify-center">
            <button class="px-4 py-2 rounded-full bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium shadow-md hover:shadow-lg transition-all duration-300">Tous</button>
            <button class="px-4 py-2 rounded-full bg-white text-gray-700 border border-gray-300 font-medium shadow-sm hover:bg-gray-100 transition-all duration-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700">Électronique</button>
            <button class="px-4 py-2 rounded-full bg-white text-gray-700 border border-gray-300 font-medium shadow-sm hover:bg-gray-100 transition-all duration-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700">Mode</button>
            <button class="px-4 py-2 rounded-full bg-white text-gray-700 border border-gray-300 font-medium shadow-sm hover:bg-gray-100 transition-all duration-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700">Maison</button>
        </div>

        <!-- Grille de produits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Carte de produit 1 - Modèle amélioré -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                <div class="relative">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 h-20 w-20 rounded-full bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 blur-lg opacity-50"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-20 w-20 rounded-full bg-gradient-to-r from-pink-500/20 via-red-600/20 to-yellow-700/20 blur-lg opacity-50"></div>
                    <div class="relative z-10">
                        <div class="relative h-60">
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded">-20%</span>
                            <img class="w-full h-full object-cover" src="/public/images/produits/téléchargement.png" alt="Apple Watch">
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Électronique</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs font-medium ml-1">4.5</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Apple Watch Series 7</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow">
                                Une montre connectée élégante avec des fonctionnalités avancées pour suivre votre santé et rester connecté.
                            </p>
                            <div class="flex justify-between items-center mt-auto">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">299 €</span>
                                    <span class="ml-2 text-sm line-through text-gray-500">379 €</span>
                                </div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Par : Jeff</span>
                            </div>
                            <button class="mt-4 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte de produit 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                <div class="relative">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 h-20 w-20 rounded-full bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 blur-lg opacity-50"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-20 w-20 rounded-full bg-gradient-to-r from-pink-500/20 via-red-600/20 to-yellow-700/20 blur-lg opacity-50"></div>
                    <div class="relative z-10">
                        <div class="relative h-60">
                            <span class="absolute top-2 left-2 bg-green-500 text-white text-xs font-medium px-2 py-0.5 rounded">Nouveau</span>
                            <img class="w-full h-full object-cover" src="/public/images/produits/681879d740a7c.png" alt="Casque audio">
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Audio</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs font-medium ml-1">4.8</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Casque Bluetooth Premium</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow">
                                Un son immersif avec réduction de bruit active et une autonomie exceptionnelle de 30 heures.
                            </p>
                            <div class="flex justify-between items-center mt-auto">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">199 €</span>
                                </div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Par : Marie</span>
                            </div>
                            <button class="mt-4 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte de produit 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1 hidden md:flex">
                <div class="relative">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 h-20 w-20 rounded-full bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 blur-lg opacity-50"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-20 w-20 rounded-full bg-gradient-to-r from-pink-500/20 via-red-600/20 to-yellow-700/20 blur-lg opacity-50"></div>
                    <div class="relative z-10">
                        <div class="relative h-60">
                            <img class="w-full h-full object-cover" src="/public/images/produits/681880c705317.png" alt="Smartphone">
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Téléphonie</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs font-medium ml-1">4.2</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Smartphone Ultra HD</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow">
                                Un appareil performant avec un appareil photo de qualité professionnelle et une batterie longue durée.
                            </p>
                            <div class="flex justify-between items-center mt-auto">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">499 €</span>
                                </div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Par : Thomas</span>
                            </div>
                            <button class="mt-4 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte de produit 4 -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1 hidden lg:flex">
                <div class="relative">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 h-20 w-20 rounded-full bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 blur-lg opacity-50"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-20 w-20 rounded-full bg-gradient-to-r from-pink-500/20 via-red-600/20 to-yellow-700/20 blur-lg opacity-50"></div>
                    <div class="relative z-10">
                        <div class="relative h-60">
                            <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs font-medium px-2 py-0.5 rounded">Populaire</span>
                            <img class="w-full h-full object-cover" src="/public/images/produits/68188270086b1.png" alt="Tablette">
                        </div>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tablettes</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs font-medium ml-1">4.9</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tablette Graphique Pro</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow">
                                Idéale pour les designers et artistes, avec une sensibilité à la pression avancée et un écran haute résolution.
                            </p>
                            <div class="flex justify-between items-center mt-auto">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">349 €</span>
                                    <span class="ml-2 text-sm line-through text-gray-500">429 €</span>
                                </div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Par : Sophie</span>
                            </div>
                            <button class="mt-4 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton pour voir plus de produits -->
        <div class="mt-10 flex justify-center">
            <a href="/produit" class="px-6 py-3 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-700 font-medium hover:bg-gray-100 transition-all duration-300 flex items-center space-x-2 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                <span>Voir tous les produits</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Section Avantages -->
<section class="bg-white dark:bg-gray-900 py-12 md:py-16">
    <div class="max-w-screen-xl mx-auto px-4 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl mb-2">Pourquoi nous choisir</h2>
            <p class="text-gray-600 dark:text-gray-400">Des avantages qui font la différence</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Avantage 1 -->
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl text-center flex flex-col items-center shadow-md hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 p-3 rounded-full inline-block mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Prix imbattables</h3>
                <p class="text-gray-600 dark:text-gray-400">Nous proposons les meilleurs prix du marché grâce à nos partenariats directs avec les fabricants.</p>
            </div>
            
            <!-- Avantage 2 -->
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl text-center flex flex-col items-center shadow-md hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 p-3 rounded-full inline-block mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Qualité garantie</h3>
                <p class="text-gray-600 dark:text-gray-400">Tous nos produits sont testés et approuvés par notre équipe d'experts pour garantir leur qualité.</p>
            </div>
            
            <!-- Avantage 3 -->
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl text-center flex flex-col items-center shadow-md hover:shadow-lg transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 p-3 rounded-full inline-block mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Livraison rapide</h3>
                <p class="text-gray-600 dark:text-gray-400">Livraison express en 24h sur la majorité de nos produits et suivi en temps réel de votre commande.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Assurez-vous d'avoir démarré la session en début de script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php if (empty($_SESSION['student'])): ?>
<!-- Call to Action -->
<section class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 py-12 md:py-16">
    <div class="max-w-screen-xl mx-auto px-4 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-white sm:text-3xl mb-4">Prêt à découvrir nos produits ?</h2>
        <p class="text-white text-opacity-90 mb-8 max-w-2xl mx-auto">Rejoignez notre communauté de clients satisfaits et trouvez les produits qui correspondent à vos besoins à des prix imbattables.</p>
        <a href="/signup" class="inline-block px-6 py-3 bg-white text-gray-900 font-medium rounded-lg shadow-lg hover:bg-gray-100 transition-all duration-300">
            S'inscrire maintenant
        </a>
    </div>
</section>
<?php endif; ?>

<!-- Script pour démarrer automatiquement le tutoriel -->
<?php if (isset($_GET['show_tutorial'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Petit délai avant de démarrer le tutoriel pour s'assurer que tout est bien chargé
        setTimeout(function() {
            // Trouver et cliquer sur le bouton de tutoriel
            const tutorialBtn = document.getElementById('start-tutorial-btn');
            if (tutorialBtn) {
                tutorialBtn.click();
            }
        }, 1000);
    });
</script>
<?php endif; ?>