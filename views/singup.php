<?php
$title = 'Inscription - TrucsPasChers';
require_once __DIR__ . '/../vendor/autoload.php';

// Démarrer ou récupérer la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est déjà connecté
if (!empty($_SESSION['user_id'])) {
    // Rediriger vers la page de profil, car l'inscription n'est pas nécessaire
    header('Location: /profil');
    exit;
}

// Liste des avatars disponibles
$avatarsDir = __DIR__ . '/../public/images/profile/avatars/';
$avatars = [];
if (is_dir($avatarsDir)) {
    foreach (glob($avatarsDir . '*.svg') as $avatarPath) {
        $avatars[] = basename($avatarPath);
    }
}

$pdo = new PDO('mysql:host=localhost;dbname=tp','root','root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['username'];
    $promotion = $_POST['promotion'];
    $telephone = $_POST['telephone'];
    $avatar = isset($_POST['avatar']) ? $_POST['avatar'] : 'default.svg';
    
    // get next id
    $stmtMax = $pdo->query('SELECT MAX(id) AS max_id FROM etudiant');
    $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['max_id'] ?? 0;
    $newId = $maxId + 1;
    // insert new student
    $stmt = $pdo->prepare('INSERT INTO etudiant (nom, promotion, telephone, password, avatar) VALUES (:nom, :promotion, :telephone, :password, :avatar)');
    $stmt->execute([
        ':nom' => $nom,
        ':promotion' => $promotion,
        ':telephone' => $telephone,
        ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ':avatar' => $avatar
    ]);
    header('Location: /');
    exit;
}
?>

<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 min-h-screen py-12">
    <div class="container px-6 mx-auto">
        <div class="flex flex-col lg:flex-row items-center justify-center lg:gap-12 max-w-6xl mx-auto">
            <!-- Section gauche avec image -->
            <div class="lg:w-1/2 mb-10 lg:mb-0 lg:order-2">
                <div class="relative">
                    <div class="absolute -top-10 -right-10 w-72 h-72 bg-gradient-to-r from-blue-500/20 via-indigo-600/20 to-purple-700/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-gradient-to-r from-purple-700/20 via-indigo-600/20 to-blue-500/20 rounded-full blur-2xl"></div>
                    
                    <div class="relative p-5 lg:py-10 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg dark:from-gray-700 dark:to-gray-800 overflow-hidden">
                        <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none">
                            <svg width="400" height="400" viewBox="0 0 200 200" class="text-blue-600 dark:text-blue-400">
                                <path fill="currentColor" d="M46,-78.1C61.3,-71.3,76.4,-61.5,86.6,-47.4C96.8,-33.2,102.1,-14.8,98.9,1.9C95.7,18.5,84,33.3,71.8,45.9C59.7,58.5,47,68.9,33.1,76.1C19.1,83.3,3.8,87.3,-13.7,86.8C-31.3,86.3,-51.1,81.2,-65.2,69.8C-79.2,58.4,-87.5,40.7,-87.8,23.8C-88.1,6.9,-80.3,-9.3,-72.3,-24.1C-64.3,-38.8,-56,-52.2,-44.2,-60.5C-32.3,-68.8,-16.2,-72.1,-0.4,-71.4C15.4,-70.7,30.8,-65.9,46,-78.1Z" transform="translate(100 100)" />
                            </svg>
                        </div>
                        
                        <div class="relative z-10">
                            <img src="/public/images/logo1.png" alt="TrucsPasChers" class="h-12 mb-6 mx-auto lg:mx-0">
                            
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Rejoignez notre communauté</h2>
                            
                            <div class="space-y-4 mb-8">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-3">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300">Créez votre profil vendeur unique</p>
                                </div>
                                
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mr-3">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300">Mettez vos produits en ligne facilement</p>
                                </div>
                                
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 mr-3">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 dark:text-gray-300">Commencez à vendre et générer des revenus</p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-white dark:bg-gray-700 border-l-4 border-blue-500 rounded-r-lg shadow-md">
                                <p class="italic text-gray-600 dark:text-gray-300">"TrucsPasChers m'a permis de vendre mes produits sans effort et avec un excellent retour sur investissement !"</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-white mt-2">- Thomas D., membre depuis 2024</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section droite avec formulaire -->
            <div class="w-full lg:w-1/2 max-w-md mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl dark:border dark:border-gray-700">
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Créer un compte</h1>
                            <p class="text-gray-600 dark:text-gray-400">Rejoignez TrucsPasChers et commencez à vendre</p>
                        </div>
                        
                        <form class="space-y-6" action="#" method="post">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nom d'utilisateur
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="username" id="username" 
                                        class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                        placeholder="Votre nom d'utilisateur" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="promotion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Promotion
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="promotion" id="promotion"
                                        class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                        placeholder="Votre promotion (ex: M2 2025)" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Téléphone
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <input type="tel" name="telephone" id="telephone" 
                                        class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                        placeholder="Votre numéro de téléphone" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Mot de passe
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input type="password" name="password" id="password" 
                                        class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                        placeholder="••••••••" required>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimum 8 caractères avec chiffres et lettres</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Choisissez un avatar
                                </label>
                                <div class="flex flex-col md:flex-row gap-6 mb-4">
                                    <div class="grid grid-cols-3 gap-4 flex-1">
                                        <?php foreach ($avatars as $avatar): ?>
                                            <div class="avatar-option">
                                                <input type="radio" name="avatar" id="avatar-<?php echo $avatar; ?>" 
                                                       value="<?php echo $avatar; ?>" <?php echo $avatar === 'default.svg' ? 'checked' : ''; ?> 
                                                       class="hidden peer" />
                                                <label for="avatar-<?php echo $avatar; ?>" 
                                                       class="flex flex-col items-center justify-center p-2 rounded-lg border-2 
                                                              cursor-pointer border-gray-200 dark:border-gray-700 
                                                              peer-checked:border-blue-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <img src="/public/images/profile/avatars/<?php echo $avatar; ?>" 
                                                         alt="Avatar" class="w-16 h-16 mb-1" />
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="flex flex-col items-center bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">Avatar sélectionné</p>
                                        <div class="w-24 h-24 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full p-1">
                                            <img id="avatar-preview" src="/public/images/profile/avatars/default.svg" alt="Avatar prévisualisé" class="w-full h-full rounded-full object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms" aria-describedby="terms" type="checkbox" required class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-light text-gray-500 dark:text-gray-300">J'accepte les <a class="font-medium text-blue-600 hover:underline dark:text-blue-500" href="#">Conditions d'utilisation</a></label>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800 text-white font-medium rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Créer mon compte
                            </button>
                            
                            <div class="text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Vous avez déjà un compte? 
                                    <a href="/login" class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        Se connecter
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        En vous inscrivant, vous acceptez notre 
                        <a href="#" class="underline hover:text-gray-700 dark:hover:text-gray-300">Politique de confidentialité</a> et nos 
                        <a href="#" class="underline hover:text-gray-700 dark:hover:text-gray-300">Conditions générales d'utilisation</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="/js/avatar-selector.js"></script>
