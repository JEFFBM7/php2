<?php
$title = 'Sing In';
require_once __DIR__ . '/../vendor/autoload.php';
$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Liste des avatars disponibles
$avatarsDir = __DIR__ . '/../public/images/profile/avatars/';
$avatars = [];
if (is_dir($avatarsDir)) {
    foreach (glob($avatarsDir . '*.svg') as $avatarPath) {
        $avatars[] = basename($avatarPath);
    }
}

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
<section class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
            TrucsPasChers
        </a>
        <div
            class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Create your account
                </h1>
                <form class="space-y-4 md:space-y-6" action="#" method="post">
                    <div>
                        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Username
                        </label>
                        <input type="text" name="username" id="username" placeholder="Your username" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 
                                   focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 
                                   dark:focus:border-blue-500" />
                    </div>
                    <div>
                        <label for="promotion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Promotion
                        </label>
                        <input type="text" name="promotion" id="promotion" placeholder="Your promotion" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 
                                   focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 
                                   dark:focus:border-blue-500" />
                    </div>
                    <div>
                        <label for="telephone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Telephone
                        </label>
                        <input type="text" name="telephone" id="telephone" placeholder="Your telephone" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 
                                   focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 
                                   dark:focus:border-blue-500" />
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Password
                        </label>
                        <input type="password" name="password" id="password" placeholder="Your password" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 
                                   focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 
                                   dark:focus:border-blue-500" />
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
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
                                <div class="w-24 h-24 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-full p-1">                                            <img id="avatar-preview" src="/public/images/profile/avatars/default.svg" alt="Avatar prévisualisé" class="w-full h-full rounded-full object-cover" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-lg shadow-lg
                               hover:from-blue-600 hover:via-indigo-700 hover:to-purple-800 focus:outline-none focus:ring-4 
                               focus:ring-indigo-300 text-white font-semibold py-2.5">
                        Sign up
                    </button>
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        Already have an account?
                        <a href="/login" class="font-medium text-primary-600 hover:underline dark:text-primary-500">
                            Sign in
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="/js/avatar-selector.js"></script>