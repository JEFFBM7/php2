<?php
$title = "Profil - TrucsPasChers";
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Etudiant;
use App\Model\Produit;

// Démarrer ou récupérer la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
$id = $_SESSION['user_id'];

// Gestion des messages de notification
$notification = null;
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleted':
            $notification = ['message' => 'Produit supprimé avec succès.', 'type' => 'success'];
            break;
        case 'delete_error':
            $notification = ['message' => 'Erreur lors de la suppression du produit ou action non autorisée.', 'type' => 'error'];
            break;
        case 'product_updated':
            $notification = ['message' => 'Produit mis à jour avec succès.', 'type' => 'success'];
            break;
        case 'profile_updated':
            $notification = ['message' => 'Votre profil a été mis à jour avec succès.', 'type' => 'success'];
            break;
        case 'profile_not_found':
            $notification = ['message' => 'Profil introuvable.', 'type' => 'error'];
            break;
        // Ajoutez d'autres cas pour d'autres actions si nécessaire
    }
}

// Traitement de la suppression de produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    if (isset($_POST['product_id'])) {
        $product_id_to_delete = $_POST['product_id'];
        // Vérifier que le produit appartient bien à l'utilisateur connecté avant de supprimer
        $stmtCheck = $pdo->prepare('SELECT * FROM produit WHERE id = :product_id AND etudiant_id = :etudiant_id');
        $stmtCheck->execute([':product_id' => $product_id_to_delete, ':etudiant_id' => $id]);
        if ($stmtCheck->fetch()) {
            // Supprimer l'image du produit du serveur si elle existe
            $stmtImage = $pdo->prepare('SELECT image FROM produit WHERE id = :product_id');
            $stmtImage->execute([':product_id' => $product_id_to_delete]);
            $image_name = $stmtImage->fetchColumn();
            if ($image_name && file_exists(__DIR__ . '/../public/images/' . $image_name)) {
                unlink(__DIR__ . '/../public/images/' . $image_name);
            }

            $stmtDelete = $pdo->prepare('DELETE FROM produit WHERE id = :product_id');
            $stmtDelete->execute([':product_id' => $product_id_to_delete]);
            // Rediriger pour éviter la resoumission du formulaire
            header('Location: /profil?action=deleted');
            exit;
        } else {
            // Tentative de suppression d'un produit non autorisé ou inexistant
            header('Location: /profil?action=delete_error');
            exit;
        }
    }
}

$stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
$stmt->execute(['id' => $id]);
$etudiant = $stmt->fetchObject(Etudiant::class);
if (!$etudiant) {
    echo '<p>Profil introuvable.</p>';
    exit;
}

$stmt2 = $pdo->prepare('SELECT * FROM produit WHERE etudiant_id = :id');
$stmt2->execute(['id' => $id]);
$produits = $stmt2->fetchAll(PDO::FETCH_CLASS, Produit::class);

// Détermination de la photo de profil
$profileImg = '/public/images/profile/' . $etudiant->getId() . '.png';
if (!file_exists(__DIR__ . '/../public' . $profileImg)) {
    $profileImg = '/public/images/default.png';
}
?>

<!-- Section de Notification -->
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

<!-- Bannière de l'utilisateur -->
<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4 relative">
        <!-- Élément décoratif -->
        <div class="absolute top-0 right-0 opacity-10 dark:opacity-5 hidden lg:block pointer-events-none">
            <svg width="200" height="200" viewBox="0 0 200 200">
                <path fill="currentColor" d="M46,-78.1C61.3,-71.3,76.4,-61.5,86.6,-47.4C96.8,-33.2,102.1,-14.8,98.9,1.9C95.7,18.5,84,33.3,71.8,45.9C59.7,58.5,47,68.9,33.1,76.1C19.1,83.3,3.8,87.3,-13.7,86.8C-31.3,86.3,-51.1,81.2,-65.2,69.8C-79.2,58.4,-87.5,40.7,-87.8,23.8C-88.1,6.9,-80.3,-9.3,-72.3,-24.1C-64.3,-38.8,-56,-52.2,-44.2,-60.5C-32.3,-68.8,-16.2,-72.1,-0.4,-71.4C15.4,-70.7,30.8,-65.9,46,-78.1Z" transform="translate(100 100)" />
            </svg>
        </div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
            <!-- Photo de profil avec cercle animé -->
            <div class="relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-full opacity-75 blur-sm group-hover:opacity-100 transition duration-500"></div>
                <div class="relative w-32 h-32 md:w-48 md:h-48 rounded-full overflow-hidden shadow-xl">
                    <img src="<?= htmlspecialchars($profileImg) ?>" alt="Photo de profil de <?= htmlspecialchars($etudiant->getNom()) ?>" class="w-full h-full object-cover" />
                </div>
            </div>

            <!-- Informations de profil -->
            <div class="text-center md:text-left flex-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2"><?= htmlspecialchars($etudiant->getNom()) ?></h1>
                <div class="mb-6 flex flex-wrap gap-2 justify-center md:justify-start">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium dark:bg-blue-900 dark:text-blue-200">
                        Promotion <?= htmlspecialchars($etudiant->getPromotion()) ?>
                    </span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium dark:bg-gray-700 dark:text-gray-200">
                        <?= count($produits) ?> produits
                    </span>
                </div>

                <!-- Informations de contact et statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300"><?= htmlspecialchars($etudiant->getTelephone()) ?></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">user<?= htmlspecialchars($etudiant->getId()) ?>@example.com</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">Membre depuis mai 2025</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">Paris, France</span>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-4 md:mt-0 flex flex-col gap-3">
                <a href="/edit_profile" class="flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow hover:opacity-90 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    Modifier le profil
                </a>
                <button class="flex items-center justify-center px-4 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-all duration-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Aide
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Onglets de navigation -->
<section class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex overflow-x-auto space-x-4">
            <button class="px-4 py-4 text-blue-600 border-b-2 border-blue-600 font-medium whitespace-nowrap dark:text-blue-400 dark:border-blue-400">
                Mes produits (<?= count($produits) ?>)
            </button>
            <button class="px-4 py-4 text-gray-600 hover:text-gray-800 font-medium whitespace-nowrap dark:text-gray-400 dark:hover:text-white">
                Commandes
            </button>
            <button class="px-4 py-4 text-gray-600 hover:text-gray-800 font-medium whitespace-nowrap dark:text-gray-400 dark:hover:text-white">
                Favoris
            </button>
            <button class="px-4 py-4 text-gray-600 hover:text-gray-800 font-medium whitespace-nowrap dark:text-gray-400 dark:hover:text-white">
                Paramètres
            </button>
        </div>
    </div>
</section>

<!-- Section des produits de l'utilisateur -->
<section class="bg-gray-50 dark:bg-gray-900 py-8 md:py-12 min-h-screen">
    <div class="container mx-auto px-4">
        <!-- En-tête avec bouton d'ajout -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mes produits</h2>
            <a href="/add_produit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow hover:opacity-90 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajouter un produit
            </a>
        </div>

        <!-- Grille de produits -->
        <?php if (count($produits) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($produits as $produit): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col transform hover:-translate-y-1">
                        <div class="relative">
                            <div class="absolute top-0 right-0 -mt-10 -mr-10 h-20 w-20 rounded-full bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 blur-lg opacity-50"></div>
                            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 h-20 w-20 rounded-full bg-gradient-to-r from-pink-500/20 via-red-600/20 to-yellow-700/20 blur-lg opacity-50"></div>
                            <div class="relative z-10">
                                <!-- Image du produit -->
                                <div class="relative h-60">
                                    <?php if ($produit->getImage()): ?>
                                        <img src="/images/<?= htmlspecialchars($produit->getImage()) ?>" alt="<?= htmlspecialchars($produit->getNom()) ?>" class="w-full h-full object-cover" />
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Menu d'options (trois points) -->
                                    <div class="absolute top-2 right-2">
                                        <button class="p-1 bg-white rounded-full shadow-md text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Contenu du produit -->
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2"><?= htmlspecialchars($produit->getNom()) ?></h3>

                                    <!-- Description avec limite de hauteur et fadeout -->
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow relative">
                                        <div class="h-24 overflow-hidden">
                                            <p><?= nl2br(htmlspecialchars($produit->getDescription())) ?></p>
                                        </div>
                                        <div class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white dark:from-gray-800"></div>
                                    </div>

                                    <!-- Prix et statut -->
                                    <div class="flex justify-between items-center mt-auto mb-3">
                                        <div class="flex items-center">
                                            <span class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($produit->getPrix()) ?> <?= htmlspecialchars($produit->getDevis()) ?></span>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded dark:bg-green-900 dark:text-green-200">Actif</span>
                                    </div>

                                    <!-- Boutons d'action -->
                                    <div class="grid grid-cols-2 gap-3 mt-3">
                                        <a href="/edit_produit?id=<?= htmlspecialchars($produit->getId()) ?>" class="text-center px-3 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                                            Modifier
                                        </a>
                                        <form action="/profil" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($produit->getId()) ?>">
                                            <button type="submit" class="w-full px-3 py-2 text-sm bg-white border border-gray-300 text-red-600 rounded-lg hover:bg-red-50 hover:border-red-300 transition-colors duration-200 dark:bg-gray-700 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900/20">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Carte d'ajout rapide -->
                <div class="bg-gray-50 dark:bg-gray-800/50 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl overflow-hidden flex flex-col items-center justify-center p-8 hover:bg-gray-100 dark:hover:bg-gray-700/30 transition-colors duration-300 cursor-pointer">
                    <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                        <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">Ajouter un produit</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Cliquez pour ajouter un nouveau produit à votre boutique</p>
                </div>
            </div>
        <?php else: ?>
            <!-- État vide -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center shadow-md">
                <div class="p-3 rounded-full bg-gray-100 inline-flex mb-4 dark:bg-gray-700">
                    <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Vous n'avez pas encore de produits</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Commencez à vendre dès aujourd'hui en ajoutant votre premier produit</p>
                <a href="/add_produit" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow hover:opacity-90 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ajouter mon premier produit
                </a>
            </div>
        <?php endif; ?>

        <!-- Statistiques de vente (simulées) -->
        <div class="mt-12 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Statistiques de vente</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">
                            <?= rand(50, 500) ?>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Vues ce mois-ci</div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700 flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">
                            <?= rand(0, 20) ?>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Ventes réalisées</div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700 flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 mr-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">
                            <?= rand(100, 1000) ?> €
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Revenu total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts pour l'interactivité -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simulation d'un clic sur la carte d'ajout rapide
        const addCard = document.querySelector('.border-dashed');
        if (addCard) {
            addCard.addEventListener('click', function() {
                window.location.href = '/add_produit';
            });
        }

        // Animation des badges de statut
        const statusBadges = document.querySelectorAll('.bg-green-100');
        if (statusBadges.length > 0) {
            statusBadges.forEach(badge => {
                badge.classList.add('transition-all', 'duration-500');
                setInterval(() => {
                    badge.classList.add('scale-110');
                    setTimeout(() => {
                        badge.classList.remove('scale-110');
                    }, 200);
                }, 5000);
            });
        }
    });
</script>