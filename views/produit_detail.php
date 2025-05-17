<?php
// filepath: /home/jeff-bm/Bureau/php2/views/produit_detail.php
$title = 'Détails du Produit';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Produit;
use App\Model\Etudiant;
use App\Model\Connection;

// Récupérer l'ID du produit depuis l'URL
$productId = null;
if (isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    error_log("ID récupéré depuis _GET: " . $productId);
} elseif (isset($params['id'])) { // Pour la compatibilité avec AltoRouter
    $productId = (int)$params['id'];
    error_log("ID récupéré depuis params (AltoRouter): " . $productId);
} else {
    error_log("Aucun ID trouvé. GET: " . json_encode($_GET) . ", PARAMS: " . (isset($params) ? json_encode($params) : "non défini"));
}

// Afficher l'URL actuelle pour débogage
error_log("URL actuelle dans produit_detail.php: " . $_SERVER['REQUEST_URI']);

if (!$productId) {
    // Rediriger vers la page des produits ou afficher une erreur si aucun ID n'est fourni
    error_log("Aucun ID de produit trouvé, redirection vers /produit");
    header('Location: /produit');
    exit;
}

$pdo = Connection::getInstance();

// Récupérer les informations du produit
$stmtProduit = $pdo->prepare('SELECT * FROM produit WHERE id = :id');
$stmtProduit->execute(['id' => $productId]);
$produit = $stmtProduit->fetchObject(Produit::class);

if (!$produit) {
    // Gérer le cas où le produit n'est pas trouvé
    http_response_code(404);
    echo "<h1>Produit non trouvé</h1>";
    // Vous pourriez inclure une vue 404 plus stylisée ici
    exit;
}

// Récupérer les informations du vendeur (étudiant)
$vendeur = null;
if ($produit->getEtudiantId()) {
    $stmtEtudiant = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
    $stmtEtudiant->execute(['id' => $produit->getEtudiantId()]);
    $vendeur = $stmtEtudiant->fetchObject(Etudiant::class);
}

// Récupérer quelques produits similaires (exemple simple : même catégorie, sauf le produit actuel)
$produitsSimilaires = [];
if ($produit->getCategori()) {
    $stmtSimilaires = $pdo->prepare('SELECT * FROM produit WHERE categori = :categori AND id != :id ORDER BY RAND() LIMIT 4');
    $stmtSimilaires->execute(['categori' => $produit->getCategori(), 'id' => $produit->getId()]);
    $produitsSimilaires = $stmtSimilaires->fetchAll(PDO::FETCH_CLASS, Produit::class);
}

?>

<!-- Fil d'Ariane -->
<section class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="container mx-auto px-4 py-3">
        <nav class="flex text-sm">
            <a href="/" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Accueil</a>
            <span class="mx-2 text-gray-500 dark:text-gray-400">/</span>
            <a href="/produit" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Produits</a>
            <?php if ($produit->getCategori()): ?>
                <span class="mx-2 text-gray-500 dark:text-gray-400">/</span>
                <a href="/produit?category=<?= urlencode($produit->getCategori()) ?>" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"><?= htmlspecialchars($produit->getCategori()) ?></a>
            <?php endif; ?>
            <span class="mx-2 text-gray-500 dark:text-gray-400">/</span>
            <span class="text-gray-900 dark:text-white font-medium truncate max-w-xs"><?= htmlspecialchars($produit->getNom()) ?></span>
        </nav>
    </div>
</section>

<section class="bg-gray-100 dark:bg-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4">
        <div class="lg:flex lg:gap-x-8">
            <!-- Colonne de l'image du produit -->
            <div class="lg:w-1/2 mb-8 lg:mb-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <?php if ($produit->getImage()): ?>
                        <img src="/public/images/produits/<?= htmlspecialchars($produit->getImage()) ?>" alt="<?= htmlspecialchars($produit->getNom()) ?>" class="block mx-auto h-100 object-cover rounded-lg ">
                    <?php else: ?>
                        <div class="w-full h-[400px] bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Colonne des détails du produit -->
            <div class="lg:w-1/2">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3"><?= htmlspecialchars($produit->getNom()) ?></h1>

                    <?php if ($produit->getCategori()): ?>
                        <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900 px-2 py-0.5 rounded-full mb-4 inline-block">
                            <?= htmlspecialchars($produit->getCategori()) ?>
                        </span>
                    <?php endif; ?>

                    <div class="flex items-center mb-4">
                        <?php $rating = round((rand(35, 50) / 10), 1); // Note aléatoire pour l'exemple 
                        ?>
                        <div class="flex items-center">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <svg class="w-5 h-5 <?= $i < floor($rating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">(<?= $rating ?> sur 5 étoiles)</span>
                    </div>

                    <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        <?= htmlspecialchars($produit->getPrix()) ?> <?= htmlspecialchars($produit->getDevis()) ?>
                    </p>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Description</h3>
                        <div class="text-gray-700 dark:text-gray-300 prose dark:prose-invert max-w-none">
                            <?= nl2br(htmlspecialchars($produit->getDescription())) ?>
                        </div>
                    </div>

                    <!-- Caractéristiques du produit -->
                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Caractéristiques</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <?php
                            // Simuler des caractéristiques basées sur la catégorie
                            $caracteristiques = [];
                            $categorie = $produit->getCategori();

                            if (stripos($categorie, 'Électronique') !== false || stripos($categorie, 'Audio') !== false || stripos($categorie, 'Téléphonie') !== false) {
                                $caracteristiques = [
                                    'Marque' => 'Premium Tech',
                                    'Modèle' => 'X-' . rand(1000, 9999),
                                    'État' => rand(0, 5) > 3 ? 'Neuf' : 'Très bon état',
                                    'Garantie' => rand(0, 1) ? '12 mois' : '6 mois',
                                    'Couleur' => ['Noir', 'Blanc', 'Gris', 'Bleu', 'Rouge'][rand(0, 4)]
                                ];
                            } elseif (stripos($categorie, 'Mode') !== false) {
                                $caracteristiques = [
                                    'Marque' => 'Fashion Line',
                                    'Taille' => ['S', 'M', 'L', 'XL'][rand(0, 3)],
                                    'Matière' => ['Coton', 'Polyester', 'Lin', 'Cuir', 'Laine'][rand(0, 4)],
                                    'Couleur' => ['Noir', 'Blanc', 'Bleu', 'Rouge', 'Vert'][rand(0, 4)],
                                    'Entretien' => 'Lavage à 30°C'
                                ];
                            } else {
                                $caracteristiques = [
                                    'Référence' => 'REF-' . rand(10000, 99999),
                                    'Marque' => 'TrucsPasChers',
                                    'État' => rand(0, 1) ? 'Neuf' : 'Bon état',
                                    'Origine' => 'France',
                                    'Expédition' => rand(0, 1) ? '24-48h' : '3-5 jours'
                                ];
                            }

                            foreach ($caracteristiques as $key => $value):
                            ?>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($key) ?></span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($value) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($vendeur): ?>
                        <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Vendu par</h3>
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full overflow-hidden mr-3">
                                    <?php if ($vendeur->getPhotoProfile()): ?>
                                        <img src="/public/images/profile/uploads/<?= htmlspecialchars($vendeur->getPhotoProfile()) ?>" alt="Photo de <?= htmlspecialchars($vendeur->getNom()) ?>" class="w-full h-full object-cover">
                                    <?php elseif ($vendeur->getAvatar()): ?>
                                        <img src="/public/images/profile/avatars/<?= htmlspecialchars($vendeur->getAvatar()) ?>" alt="Avatar de <?= htmlspecialchars($vendeur->getNom()) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <img src="/public/images/default.png" alt="Avatar par défaut" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium"><?= htmlspecialchars($vendeur->getNom()) ?></span>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Contact: <?= htmlspecialchars($vendeur->getEmail() ?? $vendeur->getTelephone()) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Statut du stock et badge de disponibilité -->
                    <div class="flex items-center mb-6">
                        <?php $inStock = rand(0, 10) > 1; // Simulation de stock disponible 
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $inStock ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' ?>">
                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 <?= $inStock ? 'text-green-500' : 'text-red-500' ?>" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            <?= $inStock ? 'En stock' : 'Rupture de stock' ?>
                        </span>

                        <?php if (rand(0, 3) === 0): // Affichage aléatoire d'un badge promo 
                        ?>
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Promo -<?= rand(10, 30) ?>%
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Sélecteur de quantité -->
                    <div class="mb-6">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantité</label>
                        <div class="flex">
                            <button type="button" id="decrease-quantity" class="bg-gray-200 dark:bg-gray-700 px-3 py-2 rounded-l-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" class="w-16 text-center border-gray-300 dark:border-gray-600 border-y bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-0">
                            <button type="button" id="increase-quantity" class="bg-gray-200 dark:bg-gray-700 px-3 py-2 rounded-r-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Bouton d'ajout au panier -->
                    <button data-product-id="<?= htmlspecialchars($produit->getId()) ?>" class="add-to-cart-btn w-full bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-6 py-3 font-semibold hover:opacity-90 transition-all duration-300 flex items-center justify-center text-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Ajouter au panier
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($produitsSimilaires)): ?>
    <section class="bg-gray-50 dark:bg-gray-950 py-8 md:py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Produits similaires</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($produitsSimilaires as $pSimilaire): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                        <a href="/produit/<?= $pSimilaire->getId() ?>" class="block">
                            <div class="relative h-48">
                                <?php if ($pSimilaire->getImage()): ?>
                                    <img src="/public/images/produits/<?= htmlspecialchars($pSimilaire->getImage()) ?>" alt="<?= htmlspecialchars($pSimilaire->getNom()) ?>" class="w-full h-full object-cover" />
                                <?php else: ?>
                                    <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 truncate"><?= htmlspecialchars($pSimilaire->getNom()) ?></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 truncate"><?= htmlspecialchars($pSimilaire->getCategori() ?? 'Non catégorisé') ?></p>
                                <div class="mt-auto">
                                    <span class="text-md font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($pSimilaire->getPrix()) ?> <?= htmlspecialchars($pSimilaire->getDevis()) ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du sélecteur de quantité
        const decreaseBtn = document.getElementById('decrease-quantity');
        const increaseBtn = document.getElementById('increase-quantity');
        const quantityInput = document.getElementById('quantity');

        if (decreaseBtn && increaseBtn && quantityInput) {
            decreaseBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });

            increaseBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue < 10) {
                    quantityInput.value = currentValue + 1;
                }
            });

            quantityInput.addEventListener('change', () => {
                let value = parseInt(quantityInput.value);
                if (isNaN(value) || value < 1) {
                    value = 1;
                } else if (value > 10) {
                    value = 10;
                }
                quantityInput.value = value;
            });
        }

        // Gestion du panier d'achat
        const addToCartButton = document.querySelector('.add-to-cart-btn');

        if (addToCartButton) {
            addToCartButton.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const originalText = this.innerHTML;
                const quantity = parseInt(document.getElementById('quantity')?.value || 1);

                this.disabled = true;
                this.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Ajout en cours...';

                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('productId', productId);
                formData.append('quantite', quantity);

                fetch('/cart-api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Ajouté !';
                            this.classList.remove('from-blue-500', 'via-indigo-600', 'to-purple-700');
                            this.classList.add('bg-green-600', 'hover:bg-green-700');
                            updateCartCounter(data.cartCount);

                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.classList.add('from-blue-500', 'via-indigo-600', 'to-purple-700');
                                this.classList.remove('bg-green-600', 'hover:bg-green-700');
                                this.disabled = false;
                            }, 3000);
                        } else {
                            this.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Erreur';
                            this.classList.remove('from-blue-500', 'via-indigo-600', 'to-purple-700');
                            this.classList.add('bg-red-600', 'hover:bg-red-700');
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.classList.add('from-blue-500', 'via-indigo-600', 'to-purple-700');
                                this.classList.remove('bg-red-600', 'hover:bg-red-700');
                                this.disabled = false;
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
                            this.disabled = false;
                        }, 3000);
                    });
            });
        }

        function updateCartCounter(count) {
            const cartCounter = document.getElementById('cart-counter');
            if (cartCounter) {
                cartCounter.textContent = count;
                cartCounter.classList.remove('hidden');
                cartCounter.classList.add('scale-110');
                setTimeout(() => cartCounter.classList.remove('scale-110'), 300);
            }
        }
    });
</script>