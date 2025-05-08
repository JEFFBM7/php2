<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Etudiant;
use App\Model\Produit;

session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
$id = $_SESSION['user_id'];
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

$title = "Profile ";
?>

<section class="bg-gray-50 dark:bg-gray-900 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-gray-100 mb-8">Profil de l'étudiant</h1>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg flex flex-col md:flex-row items-center md:items-start p-6">
            <div class="w-32 h-32 md:w-48 md:h-48 mb-4 md:mb-0 flex-shrink-0">
                <img src="<?= htmlspecialchars($profileImg) ?>" alt="Photo de profil" class="w-full h-full object-cover rounded-full border-2 border-primary" />
            </div>
            <div class="md:ml-6 text-center md:text-left">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-2"><?= htmlspecialchars($etudiant->getNom()) ?></h2>
                <p class="text-gray-600 dark:text-gray-400"><strong>Promotion :</strong> <?= htmlspecialchars($etudiant->getPromotion()) ?></p>
                <p class="text-gray-600 dark:text-gray-400"><strong>Téléphone :</strong> <?= htmlspecialchars($etudiant->getTelephone()) ?></p>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mt-12 mb-6">Mes Produits</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($produits as $produit) : ?>
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2"><?= htmlspecialchars($produit->getNom()) ?></h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4"><?= htmlspecialchars($produit->getDescription()) ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-primary font-bold text-lg"><?= htmlspecialchars($produit->getPrix()) ?> €</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Devis : <?= htmlspecialchars($produit->getDevis()) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>