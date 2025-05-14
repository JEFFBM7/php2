<?php
$title = 'TrucsPasChers - Produits';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Produit;
use App\Model\Etudiant;

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupérer la catégorie sélectionnée (si présente)
$categoryFilter = $_GET['category'] ?? 'all';

// Récupérer le terme de recherche (si présent)
$searchTerm = $_GET['search'] ?? '';

// Requête SQL de base avec filtrage par catégorie et/ou recherche
$sql = 'SELECT * FROM produit WHERE 1=1';
$params = [];

if ($searchTerm) {
    $sql .= ' AND nom LIKE :search';
    $params['search'] = "%$searchTerm%";
}

if ($categoryFilter !== 'all') {
    $sql .= ' AND categori = :category';
    $params['category'] = $categoryFilter;
}

$sql .= ' LIMIT 20';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll(PDO::FETCH_CLASS, Produit::class);

// Récupérer la liste des étudiants pour afficher le nom du vendeur
$query = $pdo->query('SELECT * FROM etudiant');
$etudiants = $query->fetchAll(PDO::FETCH_CLASS, Etudiant::class);

// Récupérer toutes les catégories distinctes de la base de données
$stmtCategories = $pdo->query('SELECT DISTINCT categori FROM produit WHERE categori IS NOT NULL AND categori != ""');
$categoriesFromDB = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);

// Si pas de catégories trouvées ou catégories vides, utiliser une liste par défaut
if (empty($categoriesFromDB)) {
    $categories = ['Électronique', 'Audio', 'Téléphonie', 'Tablettes', 'Accessoires', 'Mode', 'Maison', 'Sport', 'Livres', 'Autre'];
} else {
    $categories = $categoriesFromDB;
}
?>

<!-- Bannière principale -->
<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white md:text-4xl mb-3">Nos Produits</h1>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Découvrez notre sélection de produits de qualité à des prix imbattables</p>
    </div>
</section>

<!-- Section de filtrage et recherche -->
<section class="bg-gray-50 dark:bg-gray-900 py-6 border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Filtres de catégories -->
            <div class="flex flex-wrap gap-2">
                <a href="/produit" class="px-4 py-2 rounded-full <?= $categoryFilter === 'all' ? 'bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white' : 'bg-white text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700' ?> font-medium shadow-md hover:shadow-lg transition-all duration-300 text-sm">
                    Tous
                </a>
                <?php foreach ($categories as $category): ?>
                <a href="/produit?category=<?= urlencode($category) ?><?= !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '' ?>" 
                   class="px-4 py-2 rounded-full <?= $categoryFilter === $category ? 'bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white' : 'bg-white text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700' ?> font-medium shadow-sm hover:bg-gray-100 transition-all duration-300 text-sm dark:hover:bg-gray-700">
                    <?= htmlspecialchars($category) ?>
                </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Barre de recherche -->
            <div class="w-full md:w-auto md:min-w-[300px]">
                <form method="get" action="/produit" class="w-full">
                    <?php if ($categoryFilter !== 'all'): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($categoryFilter) ?>">
                    <?php endif; ?>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="search" name="search" value="<?= htmlspecialchars($searchTerm) ?>" class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Rechercher un produit..." />
                        <button type="submit" class="absolute right-1 bottom-1 top-1 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg text-sm px-4 hover:opacity-90 transition-opacity duration-200">
                            Rechercher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Grille de produits -->
<section class="bg-gray-50 dark:bg-gray-900 py-8 md:py-12 min-h-screen">
    <div class="container mx-auto px-4">
        <?php if (count($produits) > 0): ?>
            <!-- Nombre de résultats -->
            <div class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                <p><?= count($produits) ?> produit(s) trouvé(s) 
                <?= !empty($searchTerm) ? "pour \"" . htmlspecialchars($searchTerm) . "\"" : "" ?> 
                <?= $categoryFilter !== 'all' ? "dans la catégorie \"" . htmlspecialchars($categoryFilter) . "\"" : "" ?></p>
            </div>
            
            <!-- Grille de produits -->
            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($produits as $produit): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                        <a href="/produit/<?= $produit->getId() ?>" class="block">
                            <div class="relative">
                                <div class="absolute top-0 right-0 -mt-10 -mr-10 h-20 w-20 rounded-full bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 blur-lg opacity-50"></div>
                                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-20 w-20 rounded-full bg-gradient-to-r from-pink-500/20 via-red-600/20 to-yellow-700/20 blur-lg opacity-50"></div>
                                <div class="relative z-10">
                                    <!-- Image du produit avec overlay gradué -->
                                    <div class="relative  h-60">
                                        
                                        <?php if ($produit->getImage()): ?>
                                            <img
                                                src="/images/produits/<?= htmlspecialchars($produit->getImage()) ?>"
                                                alt="<?= htmlspecialchars($produit->getNom()) ?>"
                                                class="block mx-auto h-60 object-cover rounded-lg "
                                            />
                                        <?php else: ?>
                                    
                                        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Badge de prix promo simulé pour certains produits -->
                                        <?php if (rand(0, 2) === 0): ?>
                                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded">-<?= rand(10, 30) ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                        <div class="p-5 flex flex-col flex-grow">
                            <!-- En-tête avec catégorie et note -->
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    <?= $produit->getCategori() ? htmlspecialchars($produit->getCategori()) : 'Non catégorisé' ?>
                                </span>
                                <div class="flex items-center">
                                    <?php $rating = round((rand(35, 50) / 10), 1); ?>
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs font-medium ml-1"><?= $rating ?></span>
                                </div>
                            </div>
                            
                            <a href="/produit/<?= $produit->getId() ?>">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2"><?= htmlspecialchars($produit->getNom()) ?></h2>
                            </a>
                            
                            <!-- Description avec limite de hauteur et fadeout -->
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow relative">
                                <div class="h-24 overflow-hidden">
                                    <p><?= nl2br(htmlspecialchars($produit->getDescription())) ?></p>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white dark:from-gray-800"></div>
                            </div>
                            
                            <!-- Prix et vendeur -->
                            <div class="flex justify-between items-center mt-auto mb-4">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($produit->getPrix()) ?> <?= htmlspecialchars($produit->getDevis()) ?></span>
                                    <?php if (rand(0, 2) === 0): ?>
                                        <?php $oldPrice = round($produit->getPrix() * (rand(110, 130) / 100), 2); ?>
                                        <span class="ml-2 text-sm line-through text-gray-500"><?= $oldPrice ?> <?= htmlspecialchars($produit->getDevis()) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php foreach ($etudiants as $etudiant): ?>
                                    <?php if ($etudiant->getId() === $produit->getEtudiantId()): ?>
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full overflow-hidden mr-2">
                                                <?php if ($etudiant->getPhotoProfile()): ?>
                                                    <img src="/images/profile/<?= htmlspecialchars($etudiant->getPhotoProfile()) ?>" alt="Photo de <?= htmlspecialchars($etudiant->getNom()) ?>" class="w-full h-full object-cover">
                                                <?php elseif ($etudiant->getAvatar()): ?>
                                                    <img src="/images/profile/avatars/<?= htmlspecialchars($etudiant->getAvatar()) ?>" alt="Avatar de <?= htmlspecialchars($etudiant->getNom()) ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <img src="/images/default.png" alt="Avatar par défaut" class="w-full h-full object-cover">
                                                <?php endif; ?>
                                            </div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400"><?= htmlspecialchars($etudiant->getNom()) ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Bouton d'achat -->
                            <button data-product-id="<?= htmlspecialchars($produit->getId()) ?>" class="add-to-cart-btn bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Ajouter au panier
                            </button>

                            <!-- Bouton Voir Détails -->
                            <a href="/produit/<?= $produit->getId() ?>" class="mt-2 text-center bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white px-4 py-2 w-full rounded-lg shadow-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Voir Détails
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Message si aucun produit trouvé -->
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucun produit trouvé</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Nous n'avons pas trouvé de produits correspondant à votre recherche<?= $categoryFilter !== 'all' ? " dans cette catégorie" : "" ?>.</p>
                <a href="/produit" class="px-6 py-3 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow-md hover:opacity-90 transition-all duration-300">
                    Voir tous les produits
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du panier d'achat avec l'API réelle
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        
        if (addToCartButtons.length > 0) {
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    
                    // Afficher une animation de chargement
                    const originalText = this.innerHTML;
                    this.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Ajout en cours...';
                    
                    // Utiliser l'API pour ajouter l'article au panier
                    const formData = new FormData();
                    formData.append('action', 'add');
                    formData.append('productId', productId);
                    formData.append('quantite', 1);
                    
                    fetch('/cart-api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mise à jour réussie, afficher message de succès
                            this.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Ajouté !';
                            this.classList.remove('from-blue-500', 'via-indigo-600', 'to-purple-700');
                            this.classList.add('bg-green-600', 'hover:bg-green-700');
                            
                            // Mettre à jour éventuellement le compteur du panier dans le menu (à implémenter plus tard)
                            updateCartCounter(data.cartCount);
                            
                            // Réinitialiser après 3 secondes
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.classList.add('from-blue-500', 'via-indigo-600', 'to-purple-700');
                                this.classList.remove('bg-green-600', 'hover:bg-green-700');
                            }, 3000);
                        } else {
                            // En cas d'erreur, afficher un message d'erreur
                            this.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Erreur';
                            this.classList.remove('from-blue-500', 'via-indigo-600', 'to-purple-700');
                            this.classList.add('bg-red-600', 'hover:bg-red-700');
                            
                            // Réinitialiser après 3 secondes
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.classList.add('from-blue-500', 'via-indigo-600', 'to-purple-700');
                                this.classList.remove('bg-red-600', 'hover:bg-red-700');
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Erreur réseau';
                        this.classList.remove('from-blue-500', 'via-indigo-600', 'to-purple-700');
                        this.classList.add('bg-red-600', 'hover:bg-red-700');
                        
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.classList.add('from-blue-500', 'via-indigo-600', 'to-purple-700');
                            this.classList.remove('bg-red-600', 'hover:bg-red-700');
                        }, 3000);
                    });
                });
            });
        }
        
        // Fonction pour mettre à jour le compteur de panier dans la navigation
        function updateCartCounter(count) {
            const cartCounter = document.getElementById('cart-counter');
            if (cartCounter) {
                cartCounter.textContent = count;
                
                // Montrer l'élément s'il était caché
                cartCounter.classList.remove('hidden');
                
                // Ajouter une animation
                cartCounter.classList.add('scale-110');
                setTimeout(() => {
                    cartCounter.classList.remove('scale-110');
                }, 300);
            }
        }
        
        // Le code de recherche en temps réel qui existait déjà
        const input = document.querySelector('input[name="search"]');
        const grid = document.getElementById('product-grid');
        
        // Recherche en temps réel avec délai (si nécessaire)
        if (input && grid) {
            let debounceTimer;
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = this.value;
                    if (query.length >= 2 || query.length === 0) {
                        fetchResults(query);
                    }
                }, 300);
            });
        }
        
        function fetchResults(query) {
            // Récupérer la catégorie actuelle si elle existe
            const urlParams = new URLSearchParams(window.location.search);
            const category = urlParams.get('category');
            
            let searchUrl = '/search.php?search=' + encodeURIComponent(query);
            if (category) {
                searchUrl += '&category=' + encodeURIComponent(category);
            }
            
            fetch(searchUrl)
                .then(response => response.json())
                .then(data => {
                    // Mise à jour de l'affichage avec les résultats
                    updateProductGrid(data);
                })
                .catch(err => console.error(err));
        }
        
        function updateProductGrid(data) {
            if (!grid) return;
            
            if (data.length === 0) {
                grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-600 dark:text-gray-400">Nous n'avons pas trouvé de produits correspondant à votre recherche.</p>
                </div>`;
                return;
            }
            
            grid.innerHTML = data.map(p => {
                const rating = (Math.floor(Math.random() * 15) + 35) / 10;
                const hasDiscount = Math.random() > 0.7;
                const oldPrice = hasDiscount ? (parseFloat(p.prix) * (Math.random() * 0.3 + 1.1)).toFixed(2) : null;
                const discountBadge = Math.random() > 0.7 ? `<span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded">-${Math.floor(Math.random() * 20) + 10}%</span>` : '';
                const imageHtml = p.image 
                    ? `<img src="/images/${p.image}" alt="${p.nom}" class="w-full h-full object-cover" />`
                    : `<div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center"><svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>`;

                const productId = p.id;
                let imageLinkHref = '#';
                let titleLinkHref = '#';
                let detailButtonHtml = `<div class="mt-2 text-sm text-gray-500 text-center px-4 py-2 w-full">Détails non disponibles (ID: ${productId})</div>`;
                let validProductIdForCart = ''; 

                if (productId !== null && productId !== undefined && productId !== '' && !isNaN(Number(productId)) && Number(productId) > 0) {
                    const numericId = Number(productId);
                    imageLinkHref = `/produit/${numericId}`;
                    titleLinkHref = `/produit/${numericId}`;
                    detailButtonHtml = `
                        <a href="/produit/${numericId}" class="voir-details-link-dynamic mt-2 text-center bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white px-4 py-2 w-full rounded-lg shadow-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Voir Détails
                        </a>`;
                    validProductIdForCart = numericId;
                } else {
                    console.warn('Produit avec ID invalide ou manquant détecté dans updateProductGrid:', p);
                }
                
                const descriptionHtml = p.description ? p.description.replace(/\n/g, '<br>') : '';

                return `
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                    <a href="${imageLinkHref}" class="block">
                        <div class="relative">
                            <div class="absolute top-0 right-0 -mt-10 -mr-10 h-20 w-20 rounded-full bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 blur-lg opacity-50"></div>
                            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-20 w-20 rounded-full bg-gradient-to-r from-pink-500/20 via-red-600/20 to-yellow-700/20 blur-lg opacity-50"></div>
                            <div class="relative z-10">
                                <div class="relative h-60">
                                    ${imageHtml}
                                    ${discountBadge}
                                </div>
                            </div>
                        </div>
                    </a>

                    <div class="p-5 flex flex-col flex-grow">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">${p.categori || 'Non catégorisé'}</span>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="text-xs font-medium ml-1">${rating.toFixed(1)}</span>
                            </div>
                        </div>
                        <a href="${titleLinkHref}">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${p.nom}</h2>
                        </a>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow relative">
                            <div class="h-24 overflow-hidden">
                                <p>${descriptionHtml}</p>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white dark:from-gray-800"></div>
                        </div>
                        <div class="flex justify-between items-center mt-auto mb-4">
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">${p.prix} ${p.devis || 'EUR'}</span>
                                ${hasDiscount ? `<span class="ml-2 text-sm line-through text-gray-500">${oldPrice} ${p.devis || 'EUR'}</span>` : ''}
                            </div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Par: ${p.etudiant_nom || 'Vendeur'}</span>
                        </div>
                        <button data-product-id="${validProductIdForCart}" class="add-to-cart-btn bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center ${validProductIdForCart ? '' : 'opacity-50 cursor-not-allowed'}" ${validProductIdForCart ? '' : 'disabled'}>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Ajouter au panier
                        </button>
                        ${detailButtonHtml}
                    </div>
                </div>`;
            }).join('');
            
            attachAddToCartHandlers();
            attachVoirDetailsHandlers();
        }

        // Fonction pour attacher les gestionnaires d'événements aux boutons d'ajout au panier
        function attachAddToCartHandlers() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    if (!productId) {
                        console.warn('ID de produit invalide ou manquant pour le bouton Ajouter au panier.');
                        return;
                    }
                    console.log('Ajout au panier pour le produit ID:', productId);
                });
            });
        }

        // Nouvelle fonction pour attacher les gestionnaires d'événements aux liens "Voir Détails" dynamiques
        function attachVoirDetailsHandlers() {
            const voirDetailsLinks = document.querySelectorAll('.voir-details-link-dynamic');
            console.log(`attachVoirDetailsHandlers: Traitement de ${voirDetailsLinks.length} lien(s) "Voir Détails" dynamique(s).`);

            voirDetailsLinks.forEach(link => {
                // Remplacer le nœud pour s'assurer que les anciens écouteurs sont partis si cette fonction est appelée plusieurs fois.
                const newLink = link.cloneNode(true);
                if (link.parentNode) {
                    link.parentNode.replaceChild(newLink, link);
                } else {
                    // Si le lien n'a pas de parent, cela peut indiquer un problème dans la structure du DOM généré.
                    // Pour l'instant, on logue une erreur et on continue, mais cela mériterait investigation si ça arrive.
                    console.error('Le lien "Voir Détails" dynamique n\'a pas de parentNode lors du remplacement pour l\'écouteur d\'événement.', link);
                    // On tente d'attacher l'écouteur directement sur le lien original dans ce cas, bien que ce soit moins idéal.
                    // Si 'link' est déjà 'newLink' d'une itération précédente non insérée, cela pourrait être problématique.
                    // Cependant, avec la logique actuelle de innerHTML, 'link' devrait toujours être un élément frais du DOM.
                    newLink = link; // Fallback pour attacher au lien tel quel s'il n'a pas de parent.
                }
                

                // Simplement ajouter un log pour le débogage, mais permettre la navigation normale
                newLink.addEventListener('click', function(event) {
                    console.log('Lien "Voir Détails" (dynamique) cliqué. Navigation vers:', this.href);
                    // Retirer event.preventDefault() pour permettre la navigation
                });
            });
        }
    });
</script>