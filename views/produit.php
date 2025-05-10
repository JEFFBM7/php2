<?php
$title = 'TrucsPasChers - Produits';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Produit;
use App\Model\Etudiant;

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Filtrer selon le terme de recherche
$searchTerm = $_GET['search'] ?? '';
$stmt = $pdo->prepare(
    'SELECT * FROM produit WHERE nom LIKE :search LIMIT 20'
);
$stmt->execute(['search' => "%$searchTerm%"]);
$produits = $stmt->fetchAll(PDO::FETCH_CLASS, Produit::class);

$query = $pdo->query('SELECT * FROM etudiant ');
$etudiants = $query->fetchAll(PDO::FETCH_CLASS, Etudiant::class);

// Récupérer les catégories distinctes (simulation)
$categories = ['Électronique', 'Audio', 'Téléphonie', 'Tablettes', 'Accessoires'];
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
                <button class="px-4 py-2 rounded-full bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium shadow-md hover:shadow-lg transition-all duration-300 text-sm">
                    Tous
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="px-4 py-2 rounded-full bg-white text-gray-700 border border-gray-300 font-medium shadow-sm hover:bg-gray-100 transition-all duration-300 text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700">
                    <?= htmlspecialchars($category) ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Barre de recherche -->
            <div class="w-full md:w-auto md:min-w-[300px]">
                <form method="get" action="" class="w-full">
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
                <p><?= count($produits) ?> produit(s) trouvé(s) <?= !empty($searchTerm) ? "pour \"" . htmlspecialchars($searchTerm) . "\"" : "" ?></p>
            </div>
            
            <!-- Grille de produits -->
            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($produits as $produit): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                        <!-- Image du produit avec overlay gradué -->
                        <div class="relative h-60">
                            <img src="/images/<?= htmlspecialchars($produit->getImage()) ?>" alt="<?= htmlspecialchars($produit->getNom()) ?>" class="w-full h-full object-cover" />
                            <!-- Badge de prix promo simulé pour certains produits -->
                            <?php if (rand(0, 2) === 0): ?>
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded">-<?= rand(10, 30) ?>%</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Contenu du produit -->
                        <div class="p-5 flex flex-col flex-grow">
                            <!-- En-tête avec catégorie et note -->
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                    <?= $categories[array_rand($categories)] ?>
                                </span>
                                <div class="flex items-center">
                                    <?php $rating = round((rand(35, 50) / 10), 1); ?>
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-xs font-medium ml-1"><?= $rating ?></span>
                                </div>
                            </div>
                            
                            <!-- Titre et description -->
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2"><?= htmlspecialchars($produit->getNom()) ?></h2>
                            
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
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Par: <?= htmlspecialchars($etudiant->getNom()) ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Bouton d'achat -->
                            <button class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Ajouter au panier
                            </button>
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
                <p class="text-gray-600 dark:text-gray-400 mb-6">Nous n'avons pas trouvé de produits correspondant à votre recherche.</p>
                <a href="/produit" class="px-6 py-3 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow-md hover:opacity-90 transition-all duration-300">
                    Voir tous les produits
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.querySelector('input[name="search"]');
        const grid = document.getElementById('product-grid');
        const categories = <?= json_encode($categories) ?>;
        
        // Recherche en temps réel avec délai
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
        
        function fetchResults(query) {
            fetch('/search.php?search=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
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
                        const randomCategory = categories[Math.floor(Math.random() * categories.length)];
                        const rating = (Math.floor(Math.random() * 15) + 35) / 10;
                        const hasDiscount = Math.random() > 0.7;
                        const oldPrice = hasDiscount ? (parseFloat(p.prix) * (Math.random() * 0.3 + 1.1)).toFixed(2) : null;
                        const discountBadge = Math.random() > 0.7 ? `<span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded">-${Math.floor(Math.random() * 20) + 10}%</span>` : '';
                        
                        return `
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                            <div class="relative h-60">
                                <img src="/images/${p.image}" alt="${p.nom}" class="w-full h-full object-cover" />
                                ${discountBadge}
                            </div>
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">${randomCategory}</span>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span class="text-xs font-medium ml-1">${rating.toFixed(1)}</span>
                                    </div>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${p.nom}</h2>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow relative">
                                    <div class="h-24 overflow-hidden">
                                        <p>${p.description.replace(/\n/g, '<br>')}</p>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white dark:from-gray-800"></div>
                                </div>
                                <div class="flex justify-between items-center mt-auto mb-4">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">${p.prix} ${p.devis}</span>
                                        ${hasDiscount ? `<span class="ml-2 text-sm line-through text-gray-500">${oldPrice} ${p.devis}</span>` : ''}
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Par: ${p.etudiant_nom || 'Vendeur'}</span>
                                </div>
                                <button class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full font-medium hover:opacity-90 transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>`;
                    }).join('');
                })
                .catch(err => console.error(err));
        }
        
        // Gestion des filtres de catégorie (simulation)
        document.querySelectorAll('.rounded-full').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.rounded-full').forEach(b => {
                    b.classList.remove('bg-gradient-to-r', 'from-blue-500', 'via-indigo-600', 'to-purple-700', 'text-white');
                    b.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                });
                
                this.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                this.classList.add('bg-gradient-to-r', 'from-blue-500', 'via-indigo-600', 'to-purple-700', 'text-white');
            });
        });
    });
</script>