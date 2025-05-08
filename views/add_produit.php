<?php
$title = 'Add Produit';

require_once __DIR__ . '/../vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['name'];
    $prix = $_POST['price'];
    $description = $_POST['description'];
    $devis = $_POST['devis'] ?? 'USD';
    // Gestion de l'upload d'image
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = __DIR__ . '/../public/images/' . $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $destination);
        $imagePath = $filename;
    }
    // Valeurs par défaut
    $etudiant_id = 1;
    $stmt = $pdo->prepare(
        'INSERT INTO produit (etudiant_id, nom, description, image, prix, devis) VALUES (:etudiant_id, :nom, :description, :image, :prix, :devis)'
    );
    $stmt->execute([
        ':etudiant_id' => $etudiant_id,
        ':nom' => $nom,
        ':description' => $description,
        ':image' => $imagePath,
        ':prix' => $prix,
        ':devis' => $devis,
    ]);
    header('Location: /produit');
    exit;
}
?>

<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">
            Ajouter un nouveau produit
        </h2>
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Nom du produit
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        required
                        placeholder="Entre le nom du produit"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                     focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                                     dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                     dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                </div>

                <div class="w-full">
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Prix
                    </label>
                    <input
                        type="number"
                        name="price"
                        id="price"
                        required
                        placeholder="$..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                     focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                                     dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                     dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                </div>

                <div class="w-full">
                    <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Image du produit
                    </label>
                    <input
                        type="file"
                        name="image"
                        id="image"
                        accept="image/*"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                             focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                             dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                             dark:focus:ring-primary-500 dark:focus:border-primary-500" />
                </div>
            </div>
            <div>
                <label for="devis" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Devis
                </label>
                <select
                    name="devis"
                    id="devis"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                           focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5
                           dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                           dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    <option value="USD" selected>USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="JPY">JPY</option>
                    <!-- Ajoutez d'autres devises si nécessaire -->
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Description
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="8"
                    required
                    placeholder="Votre description ici"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300
                                     focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600
                                     dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"></textarea>
            </div>



            <div class="flex justify-end m-4">
                <button
                    type="submit"
                    class="p-3
                           bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700
                           rounded-lg shadow-lg
                           hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800
                           focus:outline-none focus:ring-4 focus:ring-indigo-300
                           text-white font-semibold py-2.5">
                    Ajouter le produit
                </button>
            </div>
    </div>

    </form>
    </div>
</section>