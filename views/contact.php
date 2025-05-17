<?php
$title = 'Contact - TrucsPasChers';
require_once __DIR__ . '/../vendor/autoload.php';
use App\Model\Etudiant;
use App\Model\Connection;

// Démarrer ou récupérer la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'étudiant connecté si disponible
$etudiant = null;
if (!empty($_SESSION['user_id'])) {
    $pdo = Connection::getInstance();
    $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $etudiant = $stmt->fetchObject(Etudiant::class);
    
    // Stocker l'objet étudiant dans la session pour y accéder facilement
    $_SESSION['student'] = $etudiant;
}

// Simuler l'envoi du formulaire
$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ici vous pourriez ajouter le code pour envoyer réellement l'e-mail
    // Pour l'instant, simulons juste un succès
    $messageSent = true;
}
?>

<section class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 py-12">
    <div class="container mx-auto px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white md:text-4xl lg:text-5xl mb-4">
                Contactez <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700">TrucsPasChers</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                Une question, un problème ou une suggestion ? Notre équipe est à votre écoute.
            </p>
        </div>

        <!-- Message de confirmation -->
        <?php if ($messageSent): ?>
        <div class="max-w-3xl mx-auto mb-8">
            <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 flex items-center">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium">Votre message a bien été envoyé !</p>
                    <p class="text-sm mt-1">Nous vous répondrons dans les plus brefs délais.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contenu principal -->
        <div class="flex flex-col lg:flex-row rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-xl">
            <!-- Partie gauche - Informations de contact -->
            <div class="lg:w-2/5 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-700 text-white p-8 lg:p-12 relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-2xl font-bold mb-6">Nos informations de contact</h2>
                    <p class="mb-8 text-blue-100">Nous sommes disponibles pour vous aider et répondre à vos questions.</p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-white/20 p-3 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold">Téléphone</h3>
                                <p class="mt-1 text-blue-100">+33 1 23 45 67 89</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-white/20 p-3 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold">Email</h3>
                                <p class="mt-1 text-blue-100">contact@trucspaschers.fr</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-white/20 p-3 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold">Adresse</h3>
                                <p class="mt-1 text-blue-100">123 Avenue des Bons Plans<br/>75000 Paris, France</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-white/20 p-3 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="font-semibold">Horaires</h3>
                                <p class="mt-1 text-blue-100">Du Lundi au Vendredi<br/>9h - 18h</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Réseaux sociaux -->
                    <div class="mt-12">
                        <h3 class="font-semibold text-lg mb-4">Suivez-nous</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-white/20 p-3 rounded-full hover:bg-white/30 transition-colors duration-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path>
                                </svg>
                            </a>
                            <a href="#" class="bg-white/20 p-3 rounded-full hover:bg-white/30 transition-colors duration-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                                </svg>
                            </a>
                            <a href="#" class="bg-white/20 p-3 rounded-full hover:bg-white/30 transition-colors duration-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                            <a href="#" class="bg-white/20 p-3 rounded-full hover:bg-white/30 transition-colors duration-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Formes décoratives en arrière-plan -->
                <div class="absolute -bottom-16 -right-16 w-64 h-64 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute -top-16 -left-16 w-64 h-64 bg-white/10 rounded-full blur-xl"></div>
            </div>
            
            <!-- Partie droite - Formulaire de contact -->
            <div class="lg:w-3/5 p-8 lg:p-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Envoyez-nous un message</h2>
                
                <form action="" method="post" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom</label>
                            <input type="text" id="first-name" name="first-name" 
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                placeholder="Votre prénom" required>
                        </div>
                        <div>
                            <label for="last-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
                            <input type="text" id="last-name" name="last-name" 
                                class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                placeholder="Votre nom" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adresse email</label>
                        <input type="email" id="email" name="email" 
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="exemple@email.com" required>
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sujet</label>
                        <select id="subject" name="subject"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="" selected disabled>Sélectionnez un sujet</option>
                            <option value="question">Question générale</option>
                            <option value="support">Support technique</option>
                            <option value="partnership">Proposition de partenariat</option>
                            <option value="feedback">Commentaires et suggestions</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Votre message</label>
                        <textarea id="message" name="message" rows="5" 
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="Comment pouvons-nous vous aider ?" required></textarea>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="privacy" type="checkbox" required
                                class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600">
                        </div>
                        <label for="privacy" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            J'accepte de recevoir des réponses par email et j'ai lu la <a href="#" class="text-blue-600 hover:underline dark:text-blue-500">politique de confidentialité</a>
                        </label>
                    </div>
                    
                    <button type="submit"
                        class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 text-white font-medium rounded-lg shadow-lg hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Section carte et FAQ -->
<section class="bg-gray-50 dark:bg-gray-900 py-12 md:py-16">
    <div class="container mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Carte -->
            <div class="rounded-xl overflow-hidden shadow-lg h-[400px] bg-gray-200 dark:bg-gray-800 relative">
                <iframe class="w-full h-full" frameborder="0" title="Carte" 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d83998.76457430434!2d2.2769953732054075!3d48.85895068246595!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e1f06e2b70f%3A0x40b82c3688c9460!2sParis%2C%20France!5e0!3m2!1sfr!2sfr!4v1651053449296!5m2!1sfr!2sfr"
                    allowfullscreen="" loading="lazy">
                </iframe>
                <div class="absolute bottom-0 left-0 right-0 bg-white dark:bg-gray-900 p-4 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Siège social</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">123 Avenue des Bons Plans, 75000 Paris</p>
                    </div>
                    <a href="https://goo.gl/maps/YZ6LbNJV6sY2oQfTA" target="_blank"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm flex items-center">
                        Itinéraire
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- FAQ -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Questions fréquentes</h2>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <button class="flex justify-between items-center w-full p-5 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <span class="font-medium text-gray-900 dark:text-white">Comment puis-je suivre ma commande ?</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-5 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400">
                                Vous pouvez suivre votre commande en vous connectant à votre compte et en accédant à la section "Mes commandes". Vous y trouverez un numéro de suivi et l'état actuel de votre livraison.
                            </p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <button class="flex justify-between items-center w-full p-5 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <span class="font-medium text-gray-900 dark:text-white">Quels sont les délais de livraison ?</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-5 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400">
                                Les délais de livraison varient généralement entre 3 et 5 jours ouvrés pour la France métropolitaine. Pour les livraisons internationales, comptez entre 5 et 10 jours ouvrés selon le pays de destination.
                            </p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <button class="flex justify-between items-center w-full p-5 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <span class="font-medium text-gray-900 dark:text-white">Comment puis-je retourner un produit ?</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-5 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400">
                                Vous disposez de 14 jours à compter de la réception pour retourner un produit. Connectez-vous à votre compte, accédez à la section "Mes commandes", sélectionnez la commande concernée et suivez les instructions pour générer une étiquette de retour.
                            </p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <button class="flex justify-between items-center w-full p-5 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <span class="font-medium text-gray-900 dark:text-white">Comment devenir vendeur sur TrucsPasChers ?</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="hidden p-5 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400">
                                Pour devenir vendeur, créez simplement un compte sur notre plateforme en cliquant sur "S'inscrire", puis complétez votre profil et ajoutez vos produits. Notre équipe validera votre demande dans les 24-48h.
                            </p>
                        </div>
                    </div>
                </div>
                
                <p class="mt-6 text-gray-600 dark:text-gray-400">
                    Vous ne trouvez pas de réponse à votre question ? 
                    <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        Consultez notre centre d'aide
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Script pour le fonctionnement des accordéons FAQ -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accordionButtons = document.querySelectorAll('.border button');
        
        accordionButtons.forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('svg');
                
                // Toggle content visibility
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    content.classList.add('hidden');
                    icon.style.transform = 'rotate(0)';
                }
                
                // Close other accordions
                accordionButtons.forEach(otherButton => {
                    if (otherButton !== button) {
                        const otherContent = otherButton.nextElementSibling;
                        const otherIcon = otherButton.querySelector('svg');
                        
                        otherContent.classList.add('hidden');
                        otherIcon.style.transform = 'rotate(0)';
                    }
                });
            });
        });
    });
</script>