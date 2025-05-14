<?php
$title = "Panier - TrucsPasChers";
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Panier;

// Démarrer ou récupérer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer le contenu du panier
$panier = Panier::getContenu();
$total = Panier::getTotal();
$nombreArticles = Panier::getNombreArticles();

// Récupérer les informations des vendeurs (étudiants)
$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupérer tous les identifiants de produits dans le panier
$produitIds = array_keys($panier);
$vendeurs = [];

if (!empty($produitIds)) {
    // Récupérer les informations des vendeurs pour ces produits
    $stmtProduits = $pdo->prepare('SELECT etudiant_id FROM produit WHERE id IN (' . implode(',', array_fill(0, count($produitIds), '?')) . ')');
    $stmtProduits->execute($produitIds);
    $etudiantIds = [];
    
    // Collecter tous les IDs d'étudiants uniques
    while ($row = $stmtProduits->fetch(PDO::FETCH_ASSOC)) {
        $etudiantIds[$row['etudiant_id']] = $row['etudiant_id'];
    }
    
    if (!empty($etudiantIds)) {
        // Récupérer les informations des étudiants
        $stmtEtudiants = $pdo->prepare('SELECT * FROM etudiant WHERE id IN (' . implode(',', array_fill(0, count($etudiantIds), '?')) . ')');
        $stmtEtudiants->execute(array_values($etudiantIds));
        
        while ($etudiant = $stmtEtudiants->fetchObject(\App\Model\Etudiant::class)) {
            $vendeurs[$etudiant->getId()] = $etudiant;
        }
    }
    
    // Associer les vendeurs aux produits du panier
    foreach ($produitIds as $produitId) {
        $stmtVendeur = $pdo->prepare('SELECT etudiant_id FROM produit WHERE id = ?');
        $stmtVendeur->execute([$produitId]);
        $etudiantId = $stmtVendeur->fetchColumn();
        if ($etudiantId && isset($vendeurs[$etudiantId])) {
            $panier[$produitId]['vendeur_id'] = $etudiantId;
        }
    }
}

// Gérer le message de notification
$notification = null;
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'added':
            $notification = ['message' => 'Produit ajouté au panier avec succès.', 'type' => 'success'];
            break;
        case 'removed':
            $notification = ['message' => 'Produit retiré du panier.', 'type' => 'success'];
            break;
        case 'updated':
            $notification = ['message' => 'Panier mis à jour.', 'type' => 'success'];
            break;
        case 'cleared':
            $notification = ['message' => 'Le panier a été vidé.', 'type' => 'success'];
            break;
        case 'error':
            $notification = ['message' => 'Une erreur est survenue.', 'type' => 'error'];
            break;
    }
}
?>

<!-- Bannière principale -->
<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white md:text-4xl mb-3">Mon Panier</h1>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Gérez vos achats et passez à la caisse</p>
    </div>
</section>

<!-- Section de notification -->
<?php if ($notification): ?>
<section class="container mx-auto px-4 mt-4">
    <div class="p-4 rounded-lg <?= $notification['type'] === 'success' ? 'bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200' ?> flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <?php if ($notification['type'] === 'success'): ?>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            <?php else: ?>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            <?php endif; ?>
        </svg>
        <?= htmlspecialchars($notification['message']) ?>
    </div>
</section>
<?php endif; ?>

<!-- Contenu du panier -->
<section class="bg-gray-50 dark:bg-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4">
        <?php if (count($panier) > 0): ?>
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Liste des produits -->
                <div class="lg:w-2/3">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Votre panier (<?= $nombreArticles ?> article<?= $nombreArticles > 1 ? 's' : '' ?>)
                            </h2>
                            <button id="clear-cart" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors duration-200">
                                Vider le panier
                            </button>
                        </div>
                        
                        <!-- Articles du panier -->
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($panier as $id => $item): ?>
                                <li class="p-6 flex flex-col sm:flex-row gap-4 cart-item" data-product-id="<?= $id ?>">
                                    <!-- Image du produit -->
                                    <div class="sm:w-24 sm:h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        <?php if ($item['image']): ?>
                                            <img src="/images/produits/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['nom']) ?>" class="w-full h-full object-cover" />
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Détails du produit -->
                                    <div class="flex-grow flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($item['nom']) ?></h3>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Prix unitaire: <?= htmlspecialchars($item['prix']) ?> <?= htmlspecialchars($item['devis']) ?></p>
                                            
                                            <?php if (isset($item['vendeur_id']) && isset($vendeurs[$item['vendeur_id']])): ?>
                                            <div class="flex items-center mt-2">
                                                <div class="w-5 h-5 rounded-full overflow-hidden mr-2">
                                                    <?php $vendeur = $vendeurs[$item['vendeur_id']]; ?>
                                                    <?php if ($vendeur->getPhotoProfile()): ?>
                                                        <img src="/images/profile/<?= htmlspecialchars($vendeur->getPhotoProfile()) ?>" alt="Photo de <?= htmlspecialchars($vendeur->getNom()) ?>" class="w-full h-full object-cover">
                                                    <?php elseif ($vendeur->getAvatar()): ?>
                                                        <img src="/images/profile/avatars/<?= htmlspecialchars($vendeur->getAvatar()) ?>" alt="Avatar de <?= htmlspecialchars($vendeur->getNom()) ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <img src="/images/default.png" alt="Avatar par défaut" class="w-full h-full object-cover">
                                                    <?php endif; ?>
                                                </div>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Vendeur: <?= htmlspecialchars($vendeur->getNom()) ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex items-center justify-between mt-4">
                                            <!-- Sélecteur de quantité -->
                                            <div class="flex items-center">
                                                <label for="quantity-<?= $id ?>" class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">Quantité:</label>
                                                <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                                    <button type="button" class="decrement-btn p-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                    </button>
                                                    <input type="number" id="quantity-<?= $id ?>" class="quantity-input w-12 p-2 text-center border-0 focus:outline-none focus:ring-0 text-gray-900 dark:text-white bg-transparent" value="<?= $item['quantite'] ?>" min="1" max="99" />
                                                    <button type="button" class="increment-btn p-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"></path></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Sous-total et bouton supprimer -->
                                            <div class="flex items-center">
                                                <span class="text-lg font-medium text-gray-900 dark:text-white mr-4 subtotal">
                                                    <?= number_format($item['prix'] * $item['quantite'], 2) ?> <?= htmlspecialchars($item['devis']) ?>
                                                </span>
                                                <button type="button" class="remove-item text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Résumé de la commande -->
                <div class="lg:w-1/3 flex flex-col gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Résumé de la commande</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Sous-total</span>
                                <span class="text-gray-900 dark:text-white"><?= number_format($total['montant'], 2) ?> <?= htmlspecialchars($total['devis']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Frais de livraison</span>
                                <span class="text-gray-900 dark:text-white">Gratuit</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                                    <span id="cart-total" class="text-lg font-bold text-gray-900 dark:text-white">
                                        <?= number_format($total['montant'], 2) ?> <?= htmlspecialchars($total['devis']) ?>
                                    </span>
                                </div>
                            </div>
                            <button id="checkout-btn" class="w-full mt-6 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow hover:opacity-90 py-3 transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                Passer à la caisse
                            </button>
                        </div>
                    </div>
                    
                    <!-- Code promo (simulé) -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Code promo</h3>
                            <form class="flex gap-2">
                                <input type="text" class="flex-grow p-2.5 text-sm border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Entrez votre code" />
                                <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600">
                                    Appliquer
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Continuer vos achats -->
                    <a href="/produit" class="text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                            </svg>
                            Continuer vos achats
                        </span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Panier vide -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center shadow-md">
                <div class="p-3 rounded-full bg-gray-100 inline-flex mb-4 dark:bg-gray-700">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Votre panier est vide</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Ajoutez des produits pour commencer vos achats.</p>
                <a href="/produit" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow hover:opacity-90 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Parcourir les produits
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Script pour les interactions du panier -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonctions de manipulation de la quantité
    const updateQuantity = (input, increment) => {
        let value = parseInt(input.value, 10);
        value = increment ? value + 1 : Math.max(1, value - 1);
        input.value = value;
        
        // Mettre à jour le sous-total
        updateItemSubtotal(input);
        
        // Envoyer la mise à jour au serveur
        const productId = input.closest('.cart-item').dataset.productId;
        updateCartItem(productId, value);
    };

    // Mise à jour du sous-total d'un article
    const updateItemSubtotal = (input) => {
        const item = input.closest('.cart-item');
        const quantity = parseInt(input.value, 10);
        
        // Récupérer le prix unitaire depuis l'élément texte
        const priceText = item.querySelector('p').textContent;
        const priceMatch = priceText.match(/Prix unitaire: ([\d.]+) ([A-Z]+)/);
        
        if (priceMatch) {
            const price = parseFloat(priceMatch[1]);
            const currency = priceMatch[2];
            const subtotal = (price * quantity).toFixed(2);
            
            // Mettre à jour le sous-total affiché
            item.querySelector('.subtotal').textContent = `${subtotal} ${currency}`;
            
            // Mettre à jour le total du panier
            updateCartTotal();
        }
    };
    
    // Calculer et mettre à jour le total du panier
    const updateCartTotal = () => {
        let total = 0;
        let currency = 'EUR';
        
        document.querySelectorAll('.cart-item').forEach(item => {
            const subtotalText = item.querySelector('.subtotal').textContent;
            const match = subtotalText.match(/([\d.]+) ([A-Z]+)/);
            
            if (match) {
                total += parseFloat(match[1]);
                currency = match[2];
            }
        });
        
        document.getElementById('cart-total').textContent = `${total.toFixed(2)} ${currency}`;
    };

    // Envoyer les mises à jour au serveur via l'API
    const updateCartItem = (productId, quantity) => {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('productId', productId);
        formData.append('quantite', quantity);
        
        fetch('/cart-api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error updating cart:', data.message);
            }
        })
        .catch(error => {
            console.error('Network error:', error);
        });
    };
    
    // Supprimer un article du panier
    const removeCartItem = (productId) => {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('productId', productId);
        
        fetch('/cart-api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirection pour rafraîchir la page
                window.location.href = '/panier?action=removed';
            } else {
                console.error('Error removing item:', data.message);
            }
        })
        .catch(error => {
            console.error('Network error:', error);
        });
    };
    
    // Vider le panier
    const clearCart = () => {
        if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
            const formData = new FormData();
            formData.append('action', 'clear');
            
            fetch('/cart-api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/panier?action=cleared';
                } else {
                    console.error('Error clearing cart:', data.message);
                }
            })
            .catch(error => {
                console.error('Network error:', error);
            });
        }
    };
    
    // Gestionnaires d'événements pour les boutons +/-
    document.querySelectorAll('.decrement-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            updateQuantity(input, false);
        });
    });
    
    document.querySelectorAll('.increment-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            updateQuantity(input, true);
        });
    });
    
    // Gestionnaire d'événement pour la modification directe de l'input
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', () => {
            // S'assurer que la valeur est au moins 1
            if (parseInt(input.value, 10) < 1) {
                input.value = 1;
            }
            
            updateItemSubtotal(input);
            
            const productId = input.closest('.cart-item').dataset.productId;
            updateCartItem(productId, input.value);
        });
    });
    
    // Gestionnaire pour les boutons de suppression
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.cart-item');
            const productId = item.dataset.productId;
            
            if (confirm('Voulez-vous supprimer cet article de votre panier ?')) {
                removeCartItem(productId);
            }
        });
    });
    
    // Gestionnaire pour le bouton "Vider le panier"
    document.getElementById('clear-cart')?.addEventListener('click', clearCart);
    
    // Gestionnaire pour le bouton "Passer à la caisse"
    document.getElementById('checkout-btn')?.addEventListener('click', () => {
        alert('Fonctionnalité de caisse en cours de développement. Merci de votre patience !');
    });
});
</script>