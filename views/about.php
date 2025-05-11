<?php
$title = 'À propos - TrucsPasChers';
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

<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-12">
    <div class="container mx-auto px-6 lg:px-8">
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
        
        <!-- En-tête de la page -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white md:text-4xl lg:text-5xl mb-4">
                À propos de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700">TrucsPasChers</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                Découvrez notre histoire, notre mission et les valeurs qui nous animent.
            </p>
            <div class="h-1 w-20 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 mx-auto"></div>
        </div>

        <!-- Notre histoire -->
        <div class="max-w-6xl mx-auto mb-24">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2 order-2 lg:order-1">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">Notre histoire</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        TrucsPasChers est né en 2023 d'une idée simple mais ambitieuse : créer une plateforme en ligne où les étudiants pourraient acheter et vendre des produits à des prix abordables. Face à l'augmentation constante du coût de la vie étudiante, nous voulions offrir une solution concrète.
                    </p>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        Fondée par un groupe d'étudiants entrepreneurs, notre plateforme a rapidement pris de l'ampleur, d'abord au sein de quelques établissements universitaires, avant de s'étendre à l'échelle nationale.
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        Aujourd'hui, TrucsPasChers représente une communauté de milliers d'étudiants qui partagent, échangent et font circuler des produits à des prix raisonnables, contribuant ainsi à une économie plus durable et responsable.
                    </p>
                </div>
                <div class="lg:w-1/2 order-1 lg:order-2 mb-8 lg:mb-0">
                    <div class="relative">
                        <div class="absolute -top-4 -left-4 w-72 h-72 bg-blue-100 dark:bg-blue-900/20 rounded-full mix-blend-multiply dark:mix-blend-normal filter blur-2xl opacity-70 animate-blob animation-delay-2000"></div>
                        <div class="absolute -bottom-8 right-4 w-72 h-72 bg-purple-100 dark:bg-purple-900/20 rounded-full mix-blend-multiply dark:mix-blend-normal filter blur-2xl opacity-70 animate-blob animation-delay-4000"></div>
                        <img class="rounded-xl shadow-xl relative z-10" src="/public/images/68188270086b1.png" alt="L'équipe de TrucsPasChers">
                    </div>
                </div>
            </div>
        </div>

        <!-- Notre mission et valeurs -->
        <div class="max-w-6xl mx-auto mb-24">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2 mb-8 lg:mb-0">
                    <div class="grid grid-cols-2 gap-4">
                        <img class="rounded-xl shadow-lg transform -rotate-2" src="/public/images/681b7bbb52f6f.png" alt="Nos produits">
                        <img class="mt-8 rounded-xl shadow-lg transform rotate-2" src="/public/images/681b7c0ea564e.png" alt="Notre communauté">
                        <img class="rounded-xl shadow-lg transform rotate-3" src="/public/images/681c78d1abcca.png" alt="Notre service">
                        <img class="mt-8 rounded-xl shadow-lg transform -rotate-3" src="/public/images/681c637b5c028.png" alt="Nos valeurs">
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">Notre mission</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        Chez TrucsPasChers, notre mission est de démocratiser l'accès aux produits de qualité pour les étudiants, tout en promouvant une consommation responsable et une économie circulaire. Nous croyons fermement que chaque étudiant mérite d'accéder à des produits de qualité sans se ruiner.
                    </p>
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">Nos valeurs</h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Accessibilité</h3>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">Nous rendons les produits abordables pour tous les étudiants, quels que soient leurs moyens financiers.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Durabilité</h3>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">Nous encourageons la réutilisation et prolongeons la vie des produits, contribuant à un avenir plus durable.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-purple-500 to-pink-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Communauté</h3>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">Nous créons des liens entre étudiants qui partagent les mêmes valeurs et aspirations.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-md bg-gradient-to-r from-pink-500 to-red-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Qualité</h3>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">Nous maintenons des standards élevés pour tous les produits proposés sur notre plateforme.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- L'équipe -->
        <div class="max-w-6xl mx-auto mb-24">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-4">Notre équipe passionnée</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Des personnes talentueuses et dévouées qui travaillent chaque jour pour améliorer votre expérience sur TrucsPasChers.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-full h-56 object-cover object-center" src="/public/images/681c88ec0c732.png" alt="Sarah Dupont">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sarah Dupont</h3>
                        <p class="text-blue-600 dark:text-blue-400 mb-3">Co-fondatrice & CEO</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Passionnée de tech et d'entrepreneuriat social, Sarah a développé TrucsPasChers pour répondre à un besoin qu'elle a elle-même connu en tant qu'étudiante.</p>
                        <div class="flex space-x-3 mt-4">
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-full h-56 object-cover object-center" src="/public/images/681c637b5c028.png" alt="Thomas Martin">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Thomas Martin</h3>
                        <p class="text-blue-600 dark:text-blue-400 mb-3">Co-fondateur & CTO</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Ingénieur en informatique de formation, Thomas est le cerveau technique derrière la plateforme TrucsPasChers.</p>
                        <div class="flex space-x-3 mt-4">
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-full h-56 object-cover object-center" src="/public/images/681879d740a7c.png" alt="Léa Bernard">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Léa Bernard</h3>
                        <p class="text-blue-600 dark:text-blue-400 mb-3">Directrice Marketing</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Avec plus de 10 ans d'expérience en marketing digital, Léa pilote la stratégie de croissance et d'acquisition de TrucsPasChers.</p>
                        <div class="flex space-x-3 mt-4">
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg transition-transform duration-300 hover:-translate-y-2">
                    <img class="w-full h-56 object-cover object-center" src="/public/images/68187a2cbc989.png" alt="Antoine Dubois">
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Antoine Dubois</h3>
                        <p class="text-blue-600 dark:text-blue-400 mb-3">Responsable des Opérations</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Antoine s'assure que la logistique et le service client fonctionnent parfaitement pour offrir la meilleure expérience possible.</p>
                        <div class="flex space-x-3 mt-4">
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Témoignages -->
        <div class="max-w-5xl mx-auto mb-20">
            <div class="text-center mb-12">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-4">Ce que disent nos utilisateurs</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Découvrez les expériences vécues par les membres de notre communauté.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg relative">
                    <div class="absolute top-0 right-0 -mt-3 -mr-3 w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center">
                        <span class="text-white text-xl">★</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        "En tant qu'étudiant avec un budget limité, TrucsPasChers m'a vraiment sauvé. J'ai pu acheter un ordinateur portable de qualité à un prix abordable qui m'accompagne dans mes études."
                    </p>
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover" src="/public/images/téléchargement.png" alt="Julien L.">
                        <div class="ml-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Julien L.</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Étudiant en droit</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg relative">
                    <div class="absolute top-0 right-0 -mt-3 -mr-3 w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center">
                        <span class="text-white text-xl">★</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        "La plateforme est intuitive et je me suis fait un peu d'argent en vendant des livres dont je n'avais plus besoin. Un excellent moyen de donner une seconde vie à nos objets !"
                    </p>
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover" src="/public/images/apple-watch.jpg" alt="Sophie D.">
                        <div class="ml-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Sophie D.</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Étudiante en informatique</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg relative">
                    <div class="absolute top-0 right-0 -mt-3 -mr-3 w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center">
                        <span class="text-white text-xl">★</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        "Le service client est réactif et efficace. J'ai eu un petit souci avec ma commande et ils l'ont résolu en moins de 24h. Je recommande vivement TrucsPasChers !"
                    </p>
                    <div class="flex items-center">
                        <img class="h-10 w-10 rounded-full object-cover" src="/public/images/téléchargement (1).jpeg" alt="Marc B.">
                        <div class="ml-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Marc B.</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Étudiant en médecine</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appel à l'action -->
        <div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-8 py-12 md:py-16 max-w-5xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-6">
                    Prêt à rejoindre notre communauté?
                </h2>
                <p class="text-blue-100 mb-8 text-lg max-w-2xl mx-auto">
                    Inscrivez-vous dès aujourd'hui et découvrez des milliers de produits à prix étudiants. Commencez à acheter et vendre en quelques clics!
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="/singup" class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-100 transition-colors duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        S'inscrire
                    </a>
                    <a href="/contact" class="inline-flex items-center justify-center px-6 py-3 border border-white text-white font-medium rounded-lg hover:bg-white/10 transition-colors duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        En savoir plus
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Style pour l'animation des blobs -->
<style>
    @keyframes blob {
        0%, 100% { transform: translate(0, 0) scale(1); }
        25% { transform: translate(20px, -20px) scale(1.1); }
        50% { transform: translate(0, 20px) scale(0.9); }
        75% { transform: translate(-20px, -10px) scale(1.05); }
    }

    .animate-blob {
        animation: blob 7s infinite;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }
</style>