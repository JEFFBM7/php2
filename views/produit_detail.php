<?php
// filepath: /home/jeff-bm/Bureau/php2/views/produit_detail.php
$title = 'Détails du Produit';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Produit;
use App\Model\Etudiant;

// Récupérer l'ID du produit depuis l'URL
$productId = null;
if (isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
} elseif (isset($params['id'])) { // Pour la compatibilité avec AltoRouter
    $productId = (int)$params['id'];
}


if (!$productId) {
    // Rediriger vers la page des produits ou afficher une erreur si aucun ID n'est fourni
    header('Location: /produit');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

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

<section class="bg-gray-100 dark:bg-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4">
        <div class="lg:flex lg:gap-x-8">
            <!-- Colonne de l'image du produit -->
            <div class="lg:w-1/2 mb-8 lg:mb-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <?php if ($produit->getImage()): ?>
                        <img src="/images/<?= htmlspecialchars($produit->getImage()) ?>" alt="<?= htmlspecialchars($produit->getNom()) ?>" class="w-full h-auto object-cover max-h-[500px]">
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
                        <?php $rating = round((rand(35, 50) / 10), 1); // Note aléatoire pour l'exemple ?>
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

                    <?php if ($vendeur): ?>
                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Vendu par</h3>
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full overflow-hidden mr-3">
                                <?php if ($vendeur->getPhotoProfile()): ?>
                                    <img src="/public/images/profile/<?= htmlspecialchars($vendeur->getPhotoProfile()) ?>" alt="Photo de <?= htmlspecialchars($vendeur->getNom()) ?>" class="w-full h-full object-cover">
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
                            <img src="/images/<?= htmlspecialchars($pSimilaire->getImage()) ?>" alt="<?= htmlspecialchars($pSimilaire->getNom()) ?>" class="w-full h-full object-cover" />
                            <?php else: ?>
                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
    // Gestion du panier d'achat (similaire à la page produit.php)
    const addToCartButton = document.querySelector('.add-to-cart-btn');
    
    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const originalText = this.innerHTML;
            
            this.disabled = true;
            this.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Ajout en cours...';
            
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('productId', productId);
            formData.append('quantite', 1); // Quantité fixe pour l'instant
            
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
