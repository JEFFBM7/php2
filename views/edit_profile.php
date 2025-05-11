<?php
$title = "Modifier le profil - TrucsPasChers";
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Etudiant;

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
$notification = null;

$stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
$stmt->execute(['id' => $id]);
$etudiant = $stmt->fetchObject(Etudiant::class);

if (!$etudiant) {
    header('Location: /profil?action=profile_not_found');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? $etudiant->getNom();
    $promotion = $_POST['promotion'] ?? $etudiant->getPromotion();
    $telephone = $_POST['telephone'] ?? $etudiant->getTelephone();
    
    // Gestion du mot de passe (optionnel)
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    $errors = [];

    if (empty($nom)) {
        $errors[] = "Le nom ne peut pas être vide.";
    }
    
    if (empty($promotion)) {
        $errors[] = "La promotion ne peut pas être vide.";
    }
    
    if (empty($telephone)) {
        $errors[] = "Le numéro de téléphone ne peut pas être vide.";
    }
    
    // Validation du mot de passe si fourni
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        } elseif ($password !== $password_confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
    }
    
    if (empty($errors)) {
        try {
            if (!empty($password)) {
                // Mise à jour avec nouveau mot de passe
                $stmtUpdate = $pdo->prepare('UPDATE etudiant SET nom = :nom, promotion = :promotion, telephone = :telephone, password = :password WHERE id = :id');
                $stmtUpdate->execute([
                    ':nom' => $nom,
                    ':promotion' => $promotion,
                    ':telephone' => $telephone,
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                    ':id' => $id
                ]);
            } else {
                // Mise à jour sans changer le mot de passe
                $stmtUpdate = $pdo->prepare('UPDATE etudiant SET nom = :nom, promotion = :promotion, telephone = :telephone WHERE id = :id');
                $stmtUpdate->execute([
                    ':nom' => $nom,
                    ':promotion' => $promotion,
                    ':telephone' => $telephone,
                    ':id' => $id
                ]);
            }
            
            // Mise à jour du nom dans la session
            $_SESSION['user_name'] = $nom;
            
            // Redirection avec message de succès
            header('Location: /profil?action=profile_updated');
            exit;
        } catch (PDOException $e) {
            $notification = ['message' => 'Une erreur est survenue lors de la mise à jour du profil : ' . $e->getMessage(), 'type' => 'error'];
        }
    } else {
        $notification = ['message' => implode(' ', $errors), 'type' => 'error'];
    }
}

// Détermination de la photo de profil
$profileImg = '/public/images/profile/' . $etudiant->getId() . '.png';
if (!file_exists(__DIR__ . '/../public' . $profileImg)) {
    $profileImg = '/public/images/default.png';
}
?>

<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-8 md:py-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white md:text-4xl mb-3">Modifier mon profil</h1>
        <p class="text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Mettez à jour vos informations personnelles</p>
    </div>
</section>

<section class="bg-gray-50 dark:bg-gray-900 py-8 md:py-12 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Message de notification -->
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
                    <!-- En-tête avec photo de profil -->
                    <div class="flex flex-col items-center mb-8">
                        <div class="relative mb-4 group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-full opacity-75 blur-sm group-hover:opacity-100 transition duration-500"></div>
                            <div class="relative w-24 h-24 rounded-full overflow-hidden shadow-lg">
                                <img src="<?= htmlspecialchars($profileImg) ?>" alt="Photo de profil" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($etudiant->getNom()) ?></h2>
                        <p class="text-gray-600 dark:text-gray-400">ID: <?= htmlspecialchars($etudiant->getId()) ?></p>
                    </div>

                    <form action="/edit_profile" method="post" class="space-y-6">
                        <!-- Nom d'utilisateur -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Nom d'utilisateur
                            </label>
                            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($etudiant->getNom()) ?>" required
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        
                        <!-- Promotion -->
                        <div>
                            <label for="promotion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Promotion
                            </label>
                            <input type="text" id="promotion" name="promotion" value="<?= htmlspecialchars($etudiant->getPromotion()) ?>" required
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Ex: M2 2025">
                        </div>
                        
                        <!-- Téléphone -->
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Numéro de téléphone
                            </label>
                            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($etudiant->getTelephone()) ?>" required
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                        
                        <!-- Section mot de passe (optionnel) -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Changer le mot de passe (optionnel)</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        Nouveau mot de passe
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <input type="password" id="password" name="password"
                                            class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                            placeholder="••••••••" minlength="8">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Laissez vide pour conserver votre mot de passe actuel</p>
                                </div>
                                
                                <div>
                                    <label for="password_confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        Confirmer le nouveau mot de passe
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                            </svg>
                                        </div>
                                        <input type="password" id="password_confirm" name="password_confirm"
                                            class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                            placeholder="••••••••" minlength="8">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-4 pt-6">
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
            
            <!-- Section de conseil de sécurité -->
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6">
                <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Conseils de sécurité
                </h3>
                <ul class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Utilisez un mot de passe fort contenant au moins 8 caractères, incluant des chiffres, des lettres majuscules et minuscules et des caractères spéciaux.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Ne partagez jamais votre mot de passe avec d'autres personnes.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Changez régulièrement votre mot de passe pour une sécurité optimale.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>