<?php
$title = 'TrucsPasChers';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Etudiant;
use App\Model\Produit;
use App\Model\Connection;

// Démarrer ou récupérer la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$pdo = Connection::getInstance();

// Récupérer les informations de l'étudiant connecté si disponible
$etudiant = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $etudiant = $stmt->fetchObject(Etudiant::class);

    // Stocker l'objet étudiant dans la session pour y accéder facilement
    $_SESSION['student'] = $etudiant;
}

// Récupérer les produits à afficher sur la page d'accueil
$stmtProduits = $pdo->query('SELECT * FROM produit ORDER BY id DESC LIMIT 8');
$produits = $stmtProduits->fetchAll(PDO::FETCH_CLASS, Produit::class);

// Récupérer la liste des étudiants pour afficher le nom du vendeur
$stmtEtudiants = $pdo->query('SELECT * FROM etudiant');
$etudiants = $stmtEtudiants->fetchAll(PDO::FETCH_CLASS, Etudiant::class);

// Récupérer toutes les catégories distinctes de la base de données
$stmtCategories = $pdo->query('SELECT DISTINCT categori FROM produit WHERE categori IS NOT NULL AND categori != ""');
$categoriesFromDB = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);

// Si pas de catégories trouvées ou catégories vides, utiliser une liste par défaut
if (empty($categoriesFromDB)) {
    $categories = ['Électronique', 'Audio', 'Téléphonie', 'Mode', 'Maison'];
} else {
    $categories = $categoriesFromDB;
}
?>
<section class="relative bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 min-h-screen overflow-hidden">
    <!-- Éléments de design en arrière-plan -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-300/20 dark:bg-purple-500/10 rounded-full filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-300/20 dark:bg-blue-500/10 rounded-full filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/3 w-96 h-96 bg-pink-300/20 dark:bg-pink-500/10 rounded-full filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="py-12 px-4 mx-auto max-w-screen-xl text-center lg:py-20 lg:px-12 relative z-10">
        <!-- Badge de notification animé -->
        <div class="flex justify-center mb-8 animate-fade-in-down">
            <a href="#produits"
                class="group inline-flex items-center py-1.5 px-3 text-sm text-gray-700 bg-white/80 backdrop-blur-sm rounded-full dark:bg-gray-800/80 dark:text-white hover:shadow-xl transition-all duration-300 border border-gray-200/50 dark:border-gray-700/50">
                <span class="flex items-center justify-center w-6 h-6 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-full text-white font-medium text-xs mr-2 shadow-inner">
                    <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-white opacity-75"></span>
                    <span>✨</span>
                </span>
                <span class="text-sm font-medium">Nouvelles promotions disponibles</span>
                <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>

        <!-- Titre principal avec animation améliorée -->
        <div class="animate-fade-in">
            <h1 class="mb-6 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white relative">
                Trouvez des produits de qualité à prix
                <span class="relative inline-flex">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700">imbattables</span>
                    <span class="absolute -bottom-1 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 transform scale-x-100 origin-left"></span>
                </span>
            </h1>

            <p class="mb-8 text-lg font-normal text-gray-600 lg:text-xl max-w-3xl mx-auto dark:text-gray-300">
                Sur TrucsPasChers, nous proposons une sélection des meilleurs produits au meilleur prix, garantis par notre communauté d'experts. <span class="hidden md:inline">Recevez votre commande en 24h!</span>
            </p>

            <!-- Boutons d'action modernisés -->
            <div class="flex flex-col mb-12 lg:mb-16 space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
                <a href="#produits"
                    class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-900 transition-all duration-300 shadow-lg hover:shadow-indigo-500/40 dark:hover:shadow-indigo-700/40">
                    Découvrir nos produits
                    <svg class="ml-2 -mr-1 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="/contact"
                    class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 transition-all duration-300 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm">
                    <svg class="mr-2 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                    </svg>
                    Nous contacter
                </a>
            </div>

            <!-- Indicateurs de confiance -->
            <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-500 dark:text-gray-400 mt-8">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Paiement sécurisé</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Livraison 24h</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span>Retours gratuits</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section des produits -->
<section id="produits" class="bg-gray-50 py-16 antialiased dark:bg-gray-900">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-8 2xl:px-0">
        <!-- Affichage de la photo de profil de l'utilisateur connecté avec design amélioré -->
        <?php if (!empty($_SESSION['student'])): ?>
            <div class="flex items-center justify-end mb-8">
                <div class="flex items-center p-2 bg-white/70 dark:bg-gray-800/70 backdrop-blur-md rounded-full shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-3">
                        Bonjour, <?= htmlspecialchars($_SESSION['student']->getNom()) ?>
                    </span>
                    <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-indigo-500 shadow-md ring-2 ring-indigo-300 dark:ring-indigo-700 group-hover:ring-indigo-500 transition-all duration-300">
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

        <!-- Titre de section avec animations -->
        <div class="mb-12 text-center animate-fade-in-up">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl mb-3 relative inline-block">
                Nos <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700">produits populaires</span>
                <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1/2 h-1 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700"></span>
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Découvrez notre sélection de produits tendance soigneusement sélectionnés pour vous</p>
        </div>

        <!-- Filtres et catégories avec design moderne -->
        <div class="mb-10">
            <div class="flex flex-col gap-4 sm:gap-0 sm:flex-row justify-between items-center">
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="/produit" class="px-4 py-2 rounded-full bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                        <span>Tous</span>
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="/produit?category=<?= urlencode($category) ?>" class="px-4 py-2 rounded-full bg-white text-gray-700 border border-gray-200 font-medium shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all duration-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 flex items-center">
                            <span><?= htmlspecialchars($category) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <!-- Ajout d'une fonction de tri -->
                <div class="relative w-full sm:w-auto">
                    <select id="product-sort" onchange="window.location.href='/produit?sort='+this.value" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 appearance-none w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-700">
                        <option value="popularity">Trier par: Popularité</option>
                        <option value="price-asc">Prix: Croissant</option>
                        <option value="price-desc">Prix: Décroissant</option>
                        <option value="newest">Nouveautés</option>
                        <option value="bestsellers">Meilleures ventes</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grille de produits avec cartes modernisées -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if (count($produits) > 0): ?>
                <?php foreach ($produits as $produit): ?>
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-500 flex flex-col transform hover:-translate-y-2 relative">
                        <!-- Badge dynamique (nouveau, promo, populaire) -->
                        <?php
                        $badgeType = rand(0, 2); // Simulation de badge aléatoire - à remplacer par une logique basée sur les données réelles
                        if ($badgeType === 0): // Réduction
                        ?>
                            <div class="absolute top-3 left-3 z-20">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-gradient-to-r from-red-500 to-pink-600 text-white text-xs font-bold shadow-lg group-hover:shadow-pink-500/30 transition-all duration-300">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                    </svg>
                                    -<?= rand(10, 30) ?>%
                                </span>
                            </div>
                        <?php elseif ($badgeType === 1): // Nouveau produit 
                        ?>
                            <div class="absolute top-3 left-3 z-20">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-bold shadow-lg group-hover:shadow-green-500/30 transition-all duration-300">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Nouveau
                                </span>
                            </div>
                        <?php elseif ($badgeType === 2): // Populaire 
                        ?>
                            <div class="absolute top-3 left-3 z-20">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-gradient-to-r from-amber-500 to-orange-600 text-white text-xs font-bold shadow-lg group-hover:shadow-amber-500/30 transition-all duration-300">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Populaire
                                </span>
                            </div>
                        <?php endif; ?>

                        <!-- Bouton favoris -->
                        <button class="absolute top-3 right-3 z-20 p-1.5 rounded-full bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm hover:bg-white dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 hover:text-pink-500 dark:hover:text-pink-500 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>

                        <!-- Image du produit avec overlay au survol -->
                        <div class="relative overflow-hidden aspect-square">
                            <?php if ($produit->getImage()): ?>
                                <img class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                    src="/public/images/produits/<?= htmlspecialchars($produit->getImage()) ?>"
                                    alt="<?= htmlspecialchars($produit->getNom()) ?>">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <a href="/produit/<?= $produit->getId() ?>">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-end justify-center pb-4">
                                    <span class="text-white text-sm font-medium px-3 py-1 bg-black/30 backdrop-blur-sm rounded-full">Voir les détails</span>
                                </div>
                            </a>
                        </div>

                        <!-- Contenu de la carte -->
                        <div class="p-5 flex flex-col flex-grow relative z-10">
                            <div class="flex justify-between items-center mb-2">
                                <span class="px-2.5 py-1 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 rounded-full">
                                    <?= $produit->getCategori() ? htmlspecialchars($produit->getCategori()) : 'Non catégorisé' ?>
                                </span>
                                <div class="flex items-center">
                                    <div class="flex">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <?php
                                        $rating = $produit->getStars() ?? round((rand(35, 50) / 10), 1);
                                        ?>
                                        <span class="text-xs font-medium ml-1"><?= $rating ?></span>
                                    </div>
                                </div>
                            </div>

                            <a href="/produit/<?= $produit->getId() ?>">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                                    <?= htmlspecialchars($produit->getNom()) ?>
                                </h3>
                            </a>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow line-clamp-2">
                                <?= nl2br(htmlspecialchars($produit->getDescription())) ?>
                            </p>

                            <!-- Prix et vendeur -->
                            <div class="flex justify-between items-center mt-auto">
                                <div class="flex items-center">
                                    <span class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                                        <?= htmlspecialchars($produit->getPrix()) ?> <?= htmlspecialchars($produit->getDevis()) ?>
                                    </span>
                                    <?php if (rand(0, 2) === 0): ?>
                                        <?php $oldPrice = round($produit->getPrix() * (rand(110, 130) / 100), 2); ?>
                                        <span class="ml-2 text-sm line-through text-gray-500">
                                            <?= $oldPrice ?> <?= htmlspecialchars($produit->getDevis()) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php foreach ($etudiants as $etudiant): ?>
                                    <?php if ($etudiant->getId() === $produit->getEtudiantId()): ?>
                                        <div class="flex items-center">
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Par : </span>
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 ml-1"><?= htmlspecialchars($etudiant->getNom()) ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <!-- Bouton d'action avec effet au survol -->
                            <button data-product-id="<?= htmlspecialchars($produit->getId()) ?>" class="add-to-cart-btn mt-4 group-hover:bg-gradient-to-br bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2.5 w-full font-medium hover:shadow-indigo-500/50 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2 transform group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="group-hover:tracking-wider transition-all duration-300">Ajouter au panier</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Affichage pour aucun produit trouvé -->
                <div class="col-span-full py-20 text-center">
                    <div class="mx-auto max-w-md">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucun produit disponible</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">Nous n'avons pas encore de produits à afficher. Revenez bientôt pour découvrir notre sélection.</p>
                        <a href="/add_produit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow-md hover:opacity-90 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter un produit
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bouton pour voir plus de produits avec effet moderne -->
        <div class="mt-12 flex justify-center">
            <a href="/produit" class="group relative inline-flex overflow-hidden rounded-full p-[2px] bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 hover:shadow-lg">
                <span class="relative inline-flex items-center space-x-3 rounded-full bg-white px-8 py-3 text-gray-800 transition-all duration-300 ease-out group-hover:bg-opacity-0 group-hover:text-white dark:bg-gray-800 dark:text-gray-200">
                    <span class="font-medium">Voir tous les produits</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </a>
        </div>

        <!-- Mobile Product Dots Navigation -->
        <div class="mt-8 flex justify-center md:hidden">
            <div class="flex space-x-2">
                <button class="w-2.5 h-2.5 bg-indigo-600 rounded-full" aria-current="true" aria-label="Page 1"></button>
                <button class="w-2.5 h-2.5 bg-gray-300 dark:bg-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-400 rounded-full transition-colors duration-300" aria-label="Page 2"></button>
                <button class="w-2.5 h-2.5 bg-gray-300 dark:bg-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-400 rounded-full transition-colors duration-300" aria-label="Page 3"></button>
                <button class="w-2.5 h-2.5 bg-gray-300 dark:bg-gray-600 hover:bg-indigo-400 dark:hover:bg-indigo-400 rounded-full transition-colors duration-300" aria-label="Page 4"></button>
            </div>
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
    <!-- Call to Action moderne avec design attrayant -->
    <section class="relative py-16 md:py-24 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700 z-0"></div>

        <!-- Éléments décoratifs -->
        <div class="absolute inset-0 z-0 overflow-hidden opacity-20">
            <div class="absolute top-0 right-0 -mt-16 -mr-16 w-80 h-80 bg-white rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-white rounded-full opacity-20 blur-3xl"></div>
            <svg class="absolute bottom-0 right-0 transform translate-x-1/4 translate-y-1/3" width="400" height="400" fill="none">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M0 40V0H40" fill="none" stroke="white" stroke-opacity="0.1" stroke-width="1"></path>
                    </pattern>
                </defs>
                <rect width="400" height="400" fill="url(#grid)"></rect>
            </svg>
        </div>

        <div class="max-w-screen-xl mx-auto px-4 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="md:w-7/12 text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-6 leading-tight">
                        Rejoignez notre communauté <br class="hidden md:block">
                        <span class="relative">
                            <span class="inline-block text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 to-yellow-400">de clients satisfaits</span>
                            <svg class="absolute -bottom-1 w-full" viewBox="0 0 300 12" xmlns="http://www.w3.org/2000/svg">
                                <path d="M275.979 1.74446C269.795 1.27996 263.588 1.59996 257.379 1.45746C250.224 1.29246 243.074 0.824961 235.921 0.674961C225.235 0.454961 214.553 0.499961 203.87 0.389961C187.883 0.224961 171.894 -0.0200391 155.908 0.00146094C142.489 0.0194609 129.07 0.389961 115.652 0.709961C106.936 0.924961 98.2265 1.51996 89.5209 1.87996C81.5396 2.21996 73.5495 2.19246 65.5677 2.53246C59.1945 2.80746 52.8121 3.12746 46.436 3.30746C45.6942 3.32996 44.9468 3.43496 44.2109 3.53996C36.1519 4.37996 28.1064 5.56746 20.0439 6.42996C16.0421 6.87246 11.9928 6.99246 8.00685 7.57996C6.18596 7.86246 4.3398 8.04746 2.5123 8.35496C1.9672 8.43746 0.5292 8.41996 0.149274 8.72246C-0.231726 9.02496 0.377905 10.2375 0.656775 10.7675C0.832941 11.0975 1.90332 11.2 2.35039 11.2225C5.69951 11.395 9.05969 11.465 12.4199 11.5675C31.3379 12.1675 50.2647 12.5175 69.1875 11.9625C83.8022 11.5275 98.4282 11.09 113.036 10.5875C130.1 10.0125 147.151 9.18496 164.221 8.88746C179.342 8.62246 194.473 9.10996 209.593 9.10996C226.993 9.10996 244.394 9.02996 261.794 9.29996C266.725 9.36996 271.655 9.57496 276.576 9.92996C277.637 10.01 278.681 10.195 279.747 10.3175C280.398 10.3975 281.056 10.475 281.706 10.55C285.02 10.885 288.333 11.2 291.647 11.53C292.057 11.5675 292.467 11.5975 292.879 11.6125C299.274 11.7925 299.274 11.7925 299.849 9.67996C300.009 9.14246 299.913 8.94246 299.376 8.65246C297.231 7.58246 295.149 6.39996 292.984 5.51496C289.264 3.99996 285.323 3.26246 281.404 2.25496C279.238 1.69996 277.608 1.89996 275.979 1.74446Z" fill="white" fill-opacity="0.5" />
                            </svg>
                        </span>
                    </h2>
                    <p class="text-xl text-white/80 mb-8 max-w-lg mx-auto md:mx-0">
                        Découvrez nos produits tendance et profitez d'offres exclusives en créant votre compte dès maintenant.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="/singup" class="inline-flex justify-center items-center py-3 px-6 text-base font-medium text-indigo-700 rounded-lg bg-white hover:bg-gray-50 focus:ring-4 focus:ring-white/30 transition-all duration-300 shadow-xl hover:shadow-white/20">
                            S'inscrire maintenant
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <a href="#produits" class="inline-flex justify-center items-center py-3 px-6 text-base font-medium text-white rounded-lg border border-white/30 hover:bg-white/10 focus:ring-4 focus:ring-white/30 transition-all duration-300 backdrop-blur-sm">
                            Voir les produits
                        </a>
                    </div>

                    <!-- Indicateurs de confiance -->
                    <div class="flex flex-wrap justify-center md:justify-start gap-6 mt-10">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="text-white/90 text-sm">4.9 - Avis clients</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span class="text-white/90 text-sm">Paiement sécurisé</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-white/90 text-sm">+10 000 clients</span>
                        </div>
                    </div>
                </div>

                <!-- Image stylisée -->
                <div class="md:w-5/12 flex justify-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-2xl blur-lg opacity-50 transform rotate-6 scale-105"></div>
                        <div class="relative bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl border border-white/20 backdrop-blur-sm">
                            <div class="p-5">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex space-x-1.5">
                                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">TrucsPasChers.fr</div>
                                </div>
                                <div class="space-y-4">
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 animate-pulse">
                                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full"></div>
                                        <div class="flex-1">
                                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 h-24 rounded-lg"></div>
                                        <div class="bg-gradient-to-br from-blue-500 to-teal-500 h-24 rounded-lg"></div>
                                    </div>
                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-full mb-2"></div>
                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Script pour les animations -->
<style>
    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }

        33% {
            transform: translate(30px, -50px) scale(1.1);
        }

        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }

        100% {
            transform: translate(0px, 0px) scale(1);
        }
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

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 1s ease-out forwards;
    }

    @keyframes fade-in-down {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-down {
        animation: fade-in-down 1s ease-out forwards;
    }

    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 1s ease-out forwards;
    }
</style>

<!-- Script pour la gestion du panier -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du panier d'achat
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

        if (addToCartButtons.length > 0) {
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');

                    // Afficher une animation de chargement
                    const originalText = this.innerHTML;
                    this.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Ajout en cours...';

                    // Appel à l'API pour ajouter au panier
                    fetch('/public/cart-api.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=add&product_id=' + productId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Afficher un feedback de succès
                                this.innerHTML = '<svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Ajouté !';

                                // Mettre à jour le compteur du panier si présent
                                const cartCounter = document.querySelector('.cart-counter');
                                if (cartCounter) {
                                    cartCounter.textContent = data.cart_count;
                                    cartCounter.classList.remove('hidden');
                                }

                                // Réinitialiser le bouton après un délai
                                setTimeout(() => {
                                    this.innerHTML = originalText;
                                }, 2000);
                            } else {
                                // Afficher un message d'erreur
                                this.innerHTML = '<svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Erreur';

                                // Réinitialiser le bouton après un délai
                                setTimeout(() => {
                                    this.innerHTML = originalText;
                                }, 2000);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            this.innerHTML = '<svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Erreur';

                            // Réinitialiser le bouton après un délai
                            setTimeout(() => {
                                this.innerHTML = originalText;
                            }, 2000);
                        });
                });
            });
        }

        // Filtrage des produits par catégorie sur la page d'accueil
        const categoryButtons = document.querySelectorAll('.category-filter-btn');
        if (categoryButtons.length > 0) {
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');

                    // Mettre à jour l'apparence des boutons
                    categoryButtons.forEach(btn => {
                        btn.classList.remove('bg-gradient-to-r', 'from-blue-500', 'via-indigo-600', 'to-purple-700', 'text-white');
                        btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-700');
                    });

                    this.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-700');
                    this.classList.add('bg-gradient-to-r', 'from-blue-500', 'via-indigo-600', 'to-purple-700', 'text-white');

                    // Redirection vers la page des produits avec le filtre
                    if (category === 'all') {
                        window.location.href = '/produit';
                    } else {
                        window.location.href = '/produit?category=' + encodeURIComponent(category);
                    }
                });
            });
        }
    });
</script>