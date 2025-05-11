<?php
$title = 'Connexion - TrucsPasChers';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Etudiant;

// Démarrer ou récupérer la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variable pour stocker le succès de connexion
$loginSuccess = false;

// Vérifier si l'utilisateur est déjà connecté
if (!empty($_SESSION['user_id'])) {
    // Rediriger vers la page de profil
    header('Location: /profil');
    exit;
}

// Traiter le formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Se connecter à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Rechercher l'utilisateur par nom d'utilisateur
    $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE nom = :nom');
    $stmt->execute([':nom' => $username]);
    $user = $stmt->fetchObject(Etudiant::class);

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user && password_verify($password, $user->getPassword())) {
        // Créer une session pour l'utilisateur
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_name'] = $user->getNom();
        
        // Vérifier si c'est le premier login pour déclencher le tutoriel
        $_SESSION['first_login'] = !$user->getLastLogin();
        
        // Mettre à jour la date de dernière connexion
        $updateStmt = $pdo->prepare('UPDATE etudiant SET last_login = NOW() WHERE id = :id');
        $updateStmt->execute([':id' => $user->getId()]);
        
        // Débogage de la redirection
        error_log("Connexion réussie pour l'utilisateur: " . $_SESSION['user_name'] . " (ID: " . $_SESSION['user_id'] . ")");
        
        // Redirection vers la page d'accueil pour le premier login (pour le tutoriel)
        if ($_SESSION['first_login']) {
            header('Location: /?show_tutorial=1');
        } else {
            // Redirection simplifiée vers la page profil pour les utilisateurs existants
            header('Location: /profil');
        }
      
        exit;
    } else {
        // Message d'erreur si les identifiants sont incorrects
        $error = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>

<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 min-h-screen flex items-center justify-center py-12">
    <div class="container px-6 mx-auto">
        <div class="flex flex-col lg:flex-row items-center justify-center lg:gap-12 max-w-6xl mx-auto">
            <!-- Section gauche avec texte d'accueil -->
            <div class="lg:w-1/2 mb-10 lg:mb-0 text-center lg:text-left">
                <a href="/" class="flex items-center justify-center lg:justify-start mb-6 space-x-3">
                    <img src="/public/images/logo1.png" alt="TrucsPasChers" class="h-12">
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 md:text-4xl lg:text-5xl">
                    Bienvenue sur <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700">TrucsPasChers</span>
                </h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg mb-8 max-w-md mx-auto lg:mx-0">
                    Connectez-vous pour accéder à votre compte et découvrir nos produits à prix imbattables.
                </p>
                
                <!-- Avantages en liste -->
                <div class="space-y-4 mb-8 max-w-md mx-auto lg:mx-0">
                    <div class="flex items-center text-left">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Produits de qualité</h3>
                            <p class="text-gray-500 dark:text-gray-400">Sélectionnés avec soin par nos experts</p>
                        </div>
                    </div>
                    <div class="flex items-center text-left">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Prix imbattables</h3>
                            <p class="text-gray-500 dark:text-gray-400">Les meilleurs tarifs du marché</p>
                        </div>
                    </div>
                    <div class="flex items-center text-left">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Livraison rapide</h3>
                            <p class="text-gray-500 dark:text-gray-400">Vos achats chez vous en 24h</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section droite avec formulaire -->
            <div class="w-full lg:w-1/2 max-w-md mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:border dark:border-gray-700 overflow-hidden">
                    <div class="relative">
                        <!-- Élément décoratif -->
                        <div class="absolute top-0 right-0 -mt-20 -mr-20 h-40 w-40 rounded-full bg-gradient-to-r from-blue-500/30 via-indigo-600/30 to-purple-700/30 blur-xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-40 w-40 rounded-full bg-gradient-to-r from-blue-500/30 via-indigo-600/30 to-purple-700/30 blur-xl"></div>
                        
                        <!-- Contenu du formulaire -->
                        <div class="p-8 relative z-10">
                            <div class="text-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Connexion</h2>
                                <p class="text-gray-600 dark:text-gray-400">Accédez à votre espace personnel</p>
                            </div>
                            
                            <form class="space-y-6" action="" method="post">
                                <?php if (isset($error)) : ?>
                                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-200 flex items-center" role="alert">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom d'utilisateur</label>
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
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Mot de passe</label>
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
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input id="remember" name="remember" type="checkbox" 
                                            class="w-4 h-4 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:focus:ring-blue-600 dark:bg-gray-700">
                                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        Mot de passe oublié?
                                    </a>
                                </div>
                                
                                <div>
                                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800 text-white font-medium rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        Se connecter
                                    </button>
                                </div>
                                
                                <div class="text-center mt-6">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Pas encore de compte? 
                                        <a href="/singup" class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            S'inscrire
                                        </a>
                                    </p>
                                </div>
                            </form>
                            
                            <div class="relative flex items-center justify-center mt-8">
                                <div class="absolute border-t border-gray-300 dark:border-gray-700 w-full"></div>
                                <div class="relative bg-white dark:bg-gray-800 px-3 text-sm text-gray-500 dark:text-gray-400">
                                    ou continuer avec
                                </div>
                            </div>
                            
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <button type="button" class="w-full flex justify-center items-center py-2.5 px-4 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                    </svg>
                                    Google
                                </button>
                                <button type="button" class="w-full flex justify-center items-center py-2.5 px-4 bg-[#1877F2] border border-[#1877F2] rounded-lg shadow-sm text-sm font-medium text-white hover:bg-[#0c65d8] transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9.5 3.5H14.5V8H21V14H14.5V24H9.5V14H3V8H9.5V3.5Z" fill-rule="evenodd" clip-rule="evenodd" />
                                    </svg>
                                    Facebook
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>