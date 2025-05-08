<?php
$title = 'Produit';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Produit;
use App\Model\Etudiant;

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// toujours filtrer selon le terme de recherche
$searchTerm = $_GET['search'] ?? '';
$stmt = $pdo->prepare(
    'SELECT * FROM produit WHERE nom LIKE :search OR description LIKE :search LIMIT 20'
);
$stmt->execute(['search' => "%$searchTerm%"]);  
$produits = $stmt->fetchAll(PDO::FETCH_CLASS, Produit::class);

$query = $pdo->query('SELECT * FROM etudiant ');
$etudiants = $query->fetchAll(PDO::FETCH_CLASS, Etudiant::class);
?>

<section class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-gray-900 dark:text-white">Produits</h1>
        <p class="mt-4 text-center text-gray-600 dark:text-gray-400">Découvrez nos produits les plus récents.</p>
    </div>

    <div class="flex justify-end m-8">
        <div class="max-w-lg w-full ml-auto">
            <label for="search-input" class="sr-only">Recherche</label>
            <input type="text" id="search-input" name="search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Recherche en temps réel..." autocomplete="off" />
        </div>
    </div>

    <div id="product-grid" class="grid grid-cols-5 gap-6 m-8">
        <?php foreach ($produits as $produit) : ?>
            <div class="bg-gray-800 rounded-2xl shadow-md overflow-hidden hover:scale-105 transition-transform duration-300">
                <div class="flex justify-center">
                    <img
                        class="h-48 max-w-lg rounded-lg"
                        src="/images/<?= htmlspecialchars($produit->getImage()) ?>"
                        alt="<?= htmlspecialchars($produit->getNom()) ?>" />
                </div>
                <div class="p-5">
                    <h2 class="text-xl font-semibold text-white mb-2"><?= $produit->getNom() ?></h2>
                    <hr class="border-gray-600 mb-2">
                    <p><?= nl2br($produit->getDescription()) ?></p>
                    <br>
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-lg font-bold"><?= $produit->getPrix() ?> <?= $produit->getDevis() ?></div>
                        <?php foreach ($etudiants as $etudiant) : ?>
                            <?php if ($etudiant->getId() === $produit->getEtudiantId()) : ?>
                                <div class="text-lg font-bold">Par : <?= $etudiant->getNom() ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <button
                        class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full hover:scale-105 transition-transform duration-300">
                        Acheter
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('search-input');
    const grid = document.getElementById('product-grid');

    input.addEventListener('input', function() {
        const query = this.value;
        fetch('/search.php?search=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                grid.innerHTML = data.map(p => `
                    <div class="bg-gray-800 rounded-2xl shadow-md overflow-hidden hover:scale-105 transition-transform duration-300">
                        <div class="flex justify-center">
                            <img class="h-48 max-w-lg rounded-lg" src="/images/${p.image}" alt="${p.nom}" />
                        </div>
                        <div class="p-5">
                            <h2 class="text-xl font-semibold text-white mb-2">${p.nom}</h2>
                            <hr class="border-gray-600 mb-2">
                            <p>${p.description.replace(/\n/g, '<br>')}</p>
                            <br>
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-lg font-bold">${p.prix} ${p.devis}</div>
                                <div class="text-lg font-bold">Par : ${p.etudiant_nom}</div>
                            </div>
                            <button class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg text-white px-4 py-2 w-full hover:scale-105 transition-transform duration-300">
                                Acheter
                            </button>
                        </div>
                    </div>
                `).join('');
            })
            .catch(err => console.error(err));
    });
});
</script>