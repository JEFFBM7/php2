<?php
$title = 'Ajouter un produit - TrucsPasChers';

require_once __DIR__ . '/../vendor/autoload.php';

// Vérifier si l'utilisateur est connecté
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

$message = null;
$messageType = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['name'];
    $prix = $_POST['price'];
    $description = $_POST['description'];
    $devis = $_POST['devis'] ?? 'EUR';
    $categorie = $_POST['categorie'] ?? 'Autre';
    
    // Gestion de l'upload d'image
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = __DIR__ . '/../public/images/' . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imagePath = $filename;
        } else {
            $message = "Erreur lors de l'upload de l'image. Veuillez réessayer.";
            $messageType = "error";
        }
    }

    if (empty($message)) {
        // Utiliser l'ID de l'utilisateur connecté
        $etudiant_id = $_SESSION['user_id'] ?? 1;
        
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO produit (etudiant_id, nom, description, image, prix, devis, categori) 
                VALUES (:etudiant_id, :nom, :description, :image, :prix, :devis, :categori)'
            );
            $stmt->execute([
                ':etudiant_id' => $etudiant_id,
                ':nom' => $nom,
                ':description' => $description,
                ':image' => $imagePath,
                ':prix' => $prix,
                ':devis' => $devis,
                ':categori' => $categorie,
            ]);
            
            $message = "Votre produit a été ajouté avec succès !";
            $messageType = "success";
            
            // Redirection après un court délai pour afficher le message
            header("refresh:1;url=/produit");
        } catch (PDOException $e) {
            $message = "Une erreur est survenue lors de l'ajout du produit : " . $e->getMessage();
            $messageType = "error";
        }
    }
}

// Catégories de produits (à ajuster selon vos besoins)
$categories = ['Électronique', 'Audio', 'Téléphonie', 'Tablettes', 'Accessoires', 'Mode', 'Maison', 'Sport', 'Livres', 'Autre'];
?>

<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white md:text-4xl mb-3">Ajouter un produit</h1>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Partagez votre produit avec la communauté TrucsPasChers et commencez à vendre dès aujourd'hui</p>
    </div>
</section>

<section class="bg-gray-50 dark:bg-gray-900 py-8 md:py-12 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Message de notification -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200' ?> flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <?php if ($messageType === 'success'): ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <?php endif; ?>
                    </svg>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulaire en carte avec ombre et coins arrondis -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Informations du produit
                    </h2>
                    
                    <form action="#" method="post" enctype="multipart/form-data" class="space-y-6">
                        <!-- Prévisualisation de l'image -->
                        <div class="mb-6">
                            <div class="flex justify-center mb-4">
                                <div class="w-full h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex flex-col items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 relative overflow-hidden" id="image-preview-container">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mb-2" id="default-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-center" id="default-text">
                                        Cliquez ou glissez-déposez pour ajouter une image
                                        <br>
                                        <span class="text-sm">JPG, PNG ou GIF, max 5MB</span>
                                    </p>
                                    <img id="image-preview" class="absolute inset-0 w-full h-full object-contain hidden" alt="Prévisualisation">
                                </div>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" required
                                class="hidden"
                                onchange="previewImage(this)" />
                            <label for="image" class="flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors duration-200 text-sm font-medium text-gray-700 dark:text-gray-300 w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Choisir une image
                            </label>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom du produit -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nom du produit
                                </label>
                                <input type="text" name="name" id="name" required
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Ex: Casque audio sans fil premium" />
                            </div>
                            
                            <!-- Catégorie -->
                            <div>
                                <label for="categorie" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Catégorie
                                </label>
                                <select name="categorie" id="categorie"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?= htmlspecialchars($categorie) ?>"><?= htmlspecialchars($categorie) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Prix et devise -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        Prix
                                    </label>
                                    <input type="number" name="price" id="price" step="0.01" min="0" required
                                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="0.00" />
                                </div>
                                <div>
                                    <label for="devis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        Devise
                                    </label>
                                    <select name="devis" id="devis"
                                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        <option value="EUR" selected>EUR (€)</option>
                                        <option value="USD">USD ($)</option>
                                        <option value="GBP">GBP (£)</option>
                                        <option value="JPY">JPY (¥)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Description
                                </label>
                                <textarea name="description" id="description" rows="5" required
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Décrivez votre produit en détail (caractéristiques, état, etc.)"></textarea>
                            </div>
                            
                            <!-- Informations complémentaires -->
                            <div class="md:col-span-2">
                                <h3 class="font-medium text-gray-900 dark:text-white mb-4">Informations complémentaires</h3>
                                <div class="flex flex-col space-y-4">
                                    <div class="flex items-center">
                                        <input id="used" type="checkbox" name="condition" value="used"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="used" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Produit d'occasion
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="delivery" type="checkbox" name="delivery" value="1"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="delivery" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Livraison possible
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="negotiable" type="checkbox" name="negotiable" value="1"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="negotiable" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Prix négociable
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="/produit" class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none transition-colors duration-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                                Annuler
                            </a>
                            <button type="submit"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow-md hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800 focus:ring-4 focus:ring-blue-300 focus:outline-none transition-all duration-300 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ajouter le produit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Conseils pour bien vendre -->
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
                <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300 mb-4">Conseils pour bien vendre</h3>
                <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Ajoutez des photos claires et nettes de votre produit sous différents angles.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Rédigez une description détaillée en mentionnant l'état, l'âge et les caractéristiques du produit.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Fixez un prix compétitif en vérifiant les prix de produits similaires sur la plateforme.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Précisez si vous proposez la livraison ou uniquement le retrait en personne.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Script pour la prévisualisation d'image -->
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
            previewContainer.classList.add('bg-transparent');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Permettre le glisser-déposer
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
    dropArea.classList.add('border-blue-500');
    dropArea.classList.add('bg-blue-50');
    dropArea.classList.add('dark:bg-blue-900/20');
}

function unhighlight() {
    dropArea.classList.remove('border-blue-500');
    dropArea.classList.remove('bg-blue-50');
    dropArea.classList.remove('dark:bg-blue-900/20');
}

dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    fileInput.files = files;
    previewImage(fileInput);
}
</script>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Répondez rapidement aux demandes d'information pour maximiser vos chances de vente.</span>
                    </li>