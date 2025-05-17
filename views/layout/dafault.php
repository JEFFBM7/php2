<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/Router.php';
?>
<?php $title = $title ?? 'Mon site'; ?>
<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/avatar.css">
    <title><?= $title ?></title>
    <style>
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 0.5;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .animate-shimmer {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        .animate-pulse {
            animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .nav-indicator {
            @apply absolute -bottom-1 left-0 right-0 h-0.5 w-0 mx-auto bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-full;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover .nav-indicator {
            @apply w-4/5;
        }

        .nav-link.active .nav-indicator {
            @apply w-4/5;
        }

        .search-expanded {
            width: 280px;
        }

        /* Styles de tutoriel supprimés pour alléger le fichier */
    </style>
</head>

<body class="font-sans bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors">
    <header class="sticky top-0 z-50">
        <nav class="bg-white dark:bg-gray-800 shadow-md">
            <div class="container mx-auto px-5 py-3">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center" id="tutorial-step-1">
                        <a href="/" class="flex items-center mr-5">
                            <img src="/public/images/logo1.png" alt="TrucsPasChers Logo" class="h-11 w-auto" />
                        </a>
                    </div>

                    <!-- Navigation principale - Desktop -->
                    <div class="hidden lg:flex flex-1 items-center justify-center ml-6" id="tutorial-step-2">
                        <nav class="flex space-x-6">
                            <?php foreach ($navItems as $path => $label): ?>
                                <?php
                                // Ne pas afficher les liens de connexion et d'inscription
                                if ($path === '/login' || $path === '/singup') {
                                    continue;
                                }
                                ?>
                                <a href="<?= $path ?>"
                                    class="nav-link relative px-4 py-2.5 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 font-medium <?= $currentPath === $path ? 'active' : '' ?>">
                                    <?= $label ?>
                                    <span class="nav-indicator"></span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <!-- Partie droite: Recherche + Auth + Mode sombre -->
                    <div class="flex items-center space-x-5">
                        <!-- Barre de recherche -->
                        <div class="hidden md:block relative" x-data="{ expanded: false }" id="tutorial-step-3">
                            <div class="relative">
                                <input type="text" placeholder="Rechercher..."
                                    class="py-2 pl-9 pr-4 border border-gray-300 dark:border-gray-600 rounded-full bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    :class="{ 'search-expanded': expanded, 'w-40': !expanded }" @focus="expanded = true"
                                    @blur="expanded = false">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton Panier -->
                        <?php
                        // Récupérer le nombre d'articles dans le panier
                        require_once __DIR__ . '/../../src/Model/Panier.php';

                        use App\Model\Panier;

                        // Démarrer ou récupérer la session si ce n'est pas déjà fait
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        $nombreArticles = Panier::getNombreArticles();
                        ?>
                        <a href="/panier" class="relative p-2.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full focus:outline-none transition-colors duration-300" aria-label="Panier" id="tutorial-step-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <?php if ($nombreArticles > 0): ?>
                                <span id="cart-counter" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center transition-all duration-300 animate-pulse-slow">
                                    <?= $nombreArticles ?>
                                </span>
                            <?php else: ?>
                                <span id="cart-counter" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center transition-all duration-300 hidden">
                                    0
                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- Commutateur mode sombre -->
                        <button id="dark-mode-toggle"
                            class="p-2.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full focus:outline-none transition-colors duration-300"
                            aria-label="Toggle Dark Mode" id="tutorial-step-5">
                            <svg id="dark-mode-icon" class="w-5 h-5 hidden dark:block" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                            <svg id="light-mode-icon" class="w-5 h-5 block dark:hidden" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                        </button>

                        <!-- Séparateur vertical -->
                        <div class="hidden md:block h-6 w-px bg-gray-300 dark:bg-gray-600"></div>

                        <!-- Menus utilisateur -->
                        <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
                            <div class="relative" x-data="{ open: false }" id="tutorial-step-6-auth">
                                <button @click="open = !open" class="flex items-center focus:outline-none group"
                                    type="button">
                                    <?php
                                    $avatar = "/public/images/default.png";

                                    // Récupérer les informations de l'étudiant pour obtenir la photo de profil ou l'avatar
                                    if (isset($_SESSION['user_id'])) {
                                        try {
                                            $stmtUser = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
                                            $stmtUser->execute(['id' => $_SESSION['user_id']]);
                                            $userObj = $stmtUser->fetchObject(\App\Model\Etudiant::class);

                                            if ($userObj) {
                                                if ($userObj->getPhotoProfile()) {
                                                    $avatar = "/public/images/profile/uploads/" . $userObj->getPhotoProfile();
                                                } elseif ($userObj->getAvatar()) {
                                                    $avatar = "/public/images/profile/avatars/" . $userObj->getAvatar();
                                                }
                                            }
                                        } catch (Exception $e) {
                                            // En cas d'erreur, utiliser l'avatar par défaut
                                        }
                                    }
                                    ?>
                                    <div class="relative">
                                        <img src="<?= $avatar ?>" alt="Avatar"
                                            class="h-10 w-10 rounded-full object-cover border-2 border-transparent group-hover:border-blue-500 transition-all duration-300">
                                        <div
                                            class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 border-2 border-white dark:border-gray-800">
                                        </div>
                                    </div>
                                </button>
                                <div @click.away="open = false" x-show="open"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 shadow-lg py-1 z-50"
                                    style="display: none;">

                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?= isset($userInfo['name']) ? htmlspecialchars($userInfo['name']) : 'Utilisateur' ?>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            <?= isset($userInfo['email']) ? htmlspecialchars($userInfo['email']) : 'utilisateur@example.com' ?>
                                        </p>
                                    </div>

                                    <a href="/profil"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        Profil
                                    </a>
                                    <a href="/add_produit"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Ajouter un produit
                                    </a>
                                    <a href="/logout"
                                        class="flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-red-500 dark:text-red-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Déconnexion
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center space-x-3" id="tutorial-step-6-noauth">
                                <a href="/login"
                                    class="py-2 px-4 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Connexion
                                </a>
                                <a href="/singup"
                                    class="py-2 px-5 text-sm font-medium text-white bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg hover:shadow-lg hover:translate-y-[-1px] transition-all duration-300 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                        </path>
                                    </svg>
                                    Inscription
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Bouton menu mobile -->
                        <button id="menu-btn" class="lg:hidden p-2 ml-1 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg focus:outline-none transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu mobile -->
            <div id="mobile-menu"
                class="hidden lg:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg">
                <div class="px-4 py-3 space-y-3">
                    <!-- Barre de recherche mobile -->
                    <div class="relative my-3">
                        <input type="text" placeholder="Rechercher..."
                            class="w-full py-2.5 pl-10 pr-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Liens de navigation mobile -->
                    <div class="pt-2 pb-3 border-t border-gray-200 dark:border-gray-700">
                        <nav class="space-y-2">
                            <?php foreach ($navItems as $path => $label): ?>
                                <?php if ($path === '/login' || $path === '/singup') continue; ?>
                                <a href="<?= $path ?>"
                                    class="flex items-center px-4 py-3 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-300 <?= $currentPath === $path ? 'bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400' : '' ?>">
                                    <?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <!-- Options utilisateur mobile -->
                    <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
                        <div class="pt-2 pb-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center px-4 py-2">
                                <img src="/public/images/profile/avatars/<?= $avatar ?>" alt="Avatar" class="h-10 w-10 rounded-full mr-3 object-cover">
                                <div>
                                    <div class="text-base font-medium text-gray-800 dark:text-white">
                                        <?= isset($userInfo['name']) ? htmlspecialchars($userInfo['name']) : 'Utilisateur' ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?= isset($userInfo['email']) ? htmlspecialchars($userInfo['email']) : 'utilisateur@example.com' ?>
                                    </div>
                                </div>
                            </div>
                            <a href="/profil"
                                class="flex items-center px-4 py-2 text-base text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-1">
                                <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profil
                            </a>
                            <a href="/add_produit"
                                class="flex items-center px-4 py-2 text-base text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Ajouter un produit
                            </a>
                            <a href="/logout"
                                class="flex items-center px-4 py-2 text-base text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 mr-3 text-red-500 dark:text-red-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Déconnexion
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-4 px-4">
                                <a href="/login"
                                    class="flex justify-center items-center py-2.5 px-4 text-center text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-500 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 font-medium">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Connexion
                                </a>
                                <a href="/singup"
                                    class="flex justify-center items-center py-2.5 px-4 text-center text-white bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-md hover:shadow-lg hover:translate-y-[-1px] transition-all duration-300 font-medium">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                        </path>
                                    </svg>
                                    Inscription
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="min-h-screen">
        <?= $content ?>
    </main>

    <footer class="bg-white dark:bg-gray-800 shadow-inner mt-12" id="tutorial-step-7">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center mb-6 md:mb-0">
                    <img src="/public/images/logo1.png" alt="TrucsPasChers Logo" class="h-10 w-auto" />
                    <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white">TrucsPasChers</span>
                </div>

            </div>
            <div class="flex flex-wrap justify-center md:justify-end gap-6">
                <div class="w-full md:w-auto mb-6 md:mb-0">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Liens rapides</h3>
                    <ul class="space-y-2">
                        <li><a href="/"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">Accueil</a>
                        </li>
                        <li><a href="/produit"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">Produits</a>
                        </li>
                        <li><a href="/contact"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">Contact</a>
                        </li>
                        <li><a href="/about"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">À
                                propos</a></li>
                    </ul>
                </div>

                <div class="w-full md:w-auto mb-6 md:mb-0">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Aide et support
                    </h3>
                    <ul class="space-y-2">
                        <li><a href="#"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">FAQ</a>
                        </li>
                        <li><a href="#"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">Conditions
                                d'utilisation</a></li>
                        <li><a href="#"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">Politique
                                de confidentialité</a></li>
                    </ul>
                </div>

                <div class="w-full md:w-auto">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Suivez-nous</h3>
                    <div class="flex space-x-3">
                        <a href="#" class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                © 2025 TrucsPasChers. Tous droits réservés.
            </p>
        </div>
        </div>
    </footer>

    <script>
        // Fonctionnement du menu mobile
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Commutateur mode sombre
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const htmlElement = document.documentElement;

        // Vérifier si le mode sombre est déjà activé en préférence système ou en localStorage
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const userPreference = localStorage.getItem('darkMode');

        // Appliquer le mode initial
        if (userPreference === 'dark' || (userPreference === null && systemPrefersDark)) {
            htmlElement.classList.add('dark');
        } else {
            htmlElement.classList.remove('dark');
        }

        // Gestionnaire d'événement pour le bouton de basculement
        darkModeToggle.addEventListener('click', () => {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'light');
            } else {
                htmlElement.classList.add('dark');
                localStorage.setItem('darkMode', 'dark');
            }
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>