<?php
$title = "Modifier le produit - TrucsPasChers";
require_once __DIR__ . '/../vendor/autoload.php';

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
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$product_id = $_GET['id'] ?? null;
$etudiant_id = $_SESSION['user_id'];
$notification = null;

if (!$product_id) {
    header('Location: /profil?action=noproductid');
    exit;
}

// Récupérer les informations du produit
$stmt = $pdo->prepare('SELECT * FROM produit WHERE id = :id AND etudiant_id = :etudiant_id');
$stmt->execute([':id' => $product_id, ':etudiant_id' => $etudiant_id]);
$produit = $stmt->fetchObject(Produit::class);

if (!$produit) {
    // Produit non trouvé ou n'appartient pas à l'utilisateur
    header('Location: /profil?action=product_not_found');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['name'] ?? $produit->getNom();
    $prix = $_POST['price'] ?? $produit->getPrix();
    $description = $_POST['description'] ?? $produit->getDescription();
    $devis = $_POST['devis'] ?? $produit->getDevis();
    $categorie = $_POST['categorie'] ?? $produit->getCategori(); // Assurez-vous que getCategori() existe

    $imagePath = $produit->getImage(); // Conserver l'ancienne image par défaut

    // Gestion de l'upload d'une nouvelle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Supprimer l'ancienne image si elle existe et si une nouvelle est uploadée
        if ($imagePath && file_exists(__DIR__ . '/../public/images/produits/' . $imagePath)) {
            unlink(__DIR__ . '/../public/images/produits/' . $imagePath);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = __DIR__ . '/../public/images/produits/' . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imagePath = $filename;
        } else {
            $notification = ['message' => 'Erreur lors du téléversement de la nouvelle image.', 'type' => 'error'];
        }
    }

    if (empty($notification)) {
        try {
            $stmtUpdate = $pdo->prepare(
                'UPDATE produit SET nom = :nom, description = :description, image = :image, prix = :prix, devis = :devis, categori = :categori 
                 WHERE id = :id AND etudiant_id = :etudiant_id'
            );
            $stmtUpdate->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':image' => $imagePath,
                ':prix' => $prix,
                ':devis' => $devis,
                ':categori' => $categorie,
                ':id' => $product_id,
                ':etudiant_id' => $etudiant_id
            ]);
            
            $notification = ['message' => 'Produit mis à jour avec succès !', 'type' => 'success'];
            // Re-récupérer les données du produit pour afficher les modifications
            $stmt->execute([':id' => $product_id, ':etudiant_id' => $etudiant_id]);
            $produit = $stmt->fetchObject(Produit::class);
            header('Location: /profil?action=product_updated');
            exit;
        } catch (PDOException $e) {
            $notification = ['message' => 'Une erreur est survenue lors de la mise à jour du produit : ' . $e->getMessage(), 'type' => 'error'];
        }
    }
}

$categories = ['Électronique', 'Audio', 'Téléphonie', 'Tablettes', 'Accessoires', 'Mode', 'Maison', 'Sport', 'Livres', 'Autre'];

?>

<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white md:text-4xl mb-3">Modifier le produit</h1>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Mettez à jour les informations de votre produit.</p>
    </div>
</section>

<section class="bg-gray-50 dark:bg-gray-900 py-8 md:py-12 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <?php if ($notification): ?>
                <div class="mb-6 p-4 rounded-lg <?= $notification['type'] === 'success' ? 'bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200' ?> flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <?php if ($notification['type'] === 'success'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <?php endif; ?>
                    </svg>
                    <?= htmlspecialchars($notification['message']) ?>
                </div>
            <?php endif; ?>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Informations du produit
                    </h2>
                    
                    <form action="/edit_produit?id=<?= htmlspecialchars($produit->getId()) ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                        <div class="mb-6">
                            <div class="flex justify-center mb-4">
                                <div class="w-full h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 relative overflow-hidden" id="image-preview-container">
                                    <img id="image-preview" class="absolute inset-0 w-full h-full object-contain <?= $produit->getImage() ? '' : 'hidden' ?>" src="<?= $produit->getImage() ? '/public/images/produits/' . htmlspecialchars($produit->getImage()) : '' ?>" alt="Prévisualisation">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-2 <?= $produit->getImage() ? 'hidden' : '' ?>" id="default-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-center <?= $produit->getImage() ? 'hidden' : '' ?>" id="default-text">
                                        Cliquez ou glissez-déposez pour changer l'image
                                        <br>
                                        <span class="text-sm">JPG, PNG ou GIF, max 5MB</span>
                                    </p>
                                </div>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="hidden"
                                onchange="previewImage(this)" />
                            <label for="image" class="flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-200 text-sm font-medium text-gray-700 dark:text-gray-300 w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Changer l'image (optionnel)
                            </label>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nom du produit
                                </label>
                                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($produit->getNom()) ?>"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Ex: Casque audio sans fil premium" />
                            </div>
                            
                            <div>
                                <label for="categorie" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Catégorie
                                </label>
                                <select name="categorie" id="categorie"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat) ?>" <?= ($produit->getCategori() === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        Prix
                                    </label>
                                    <input type="number" name="price" id="price" step="0.01" min="0" required value="<?= htmlspecialchars($produit->getPrix()) ?>"
                                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="0.00" />
                                </div>
                                <div>
                                    <label for="devis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        Devise
                                    </label>
                                    <select name="devis" id="devis"
                                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        <option value="EUR" <?= ($produit->getDevis() === 'EUR') ? 'selected' : '' ?>>EUR (€)</option>
                                        <option value="USD" <?= ($produit->getDevis() === 'USD') ? 'selected' : '' ?>>USD ($)</option>
                                        <option value="GBP" <?= ($produit->getDevis() === 'GBP') ? 'selected' : '' ?>>GBP (£)</option>
                                        <option value="JPY" <?= ($produit->getDevis() === 'JPY') ? 'selected' : '' ?>>JPY (¥)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Description
                                </label>
                                <textarea name="description" id="description" rows="5" required
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Décrivez votre produit en détail (caractéristiques, état, etc.)"><?= htmlspecialchars($produit->getDescription()) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="/profil" class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none transition-colors duration-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                                Annuler
                            </a>
                            <button type="submit"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow-md hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800 focus:ring-4 focus:ring-blue-300 focus:outline-none transition-all duration-300 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const defaultIcon = document.getElementById('default-icon');
    const defaultText = document.getElementById('default-text');
    const previewContainer = document.getElementById('image-preview-container');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            defaultIcon.classList.add('hidden');
            defaultText.classList.add('hidden');
            previewContainer.classList.add('bg-transparent'); // Optionnel: rendre le fond transparent si une image est chargée
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        // Si aucune image n'est sélectionnée (par exemple, si l'utilisateur annule la sélection)
        // On pourrait vouloir réafficher l'image existante si elle y était, ou l'icône par défaut.
        // Pour l'instant, on ne fait rien, l'image existante (si présente) reste affichée.
    }
}

const dropArea = document.getElementById('image-preview-container');
const fileInput = document.getElementById('image');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

function highlight() {
    dropArea.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
}

function unhighlight() {
    dropArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
}

dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    fileInput.files = files;
    previewImage(fileInput);
}
</script>
