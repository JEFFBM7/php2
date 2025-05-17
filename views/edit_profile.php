<?php
$title = "Modifier le profil - TrucsPasChers";
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Etudiant;
use App\Model\Connection;

// Démarrer ou récupérer la session uniquement si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$pdo = Connection::getInstance();

$id = $_SESSION['user_id'];
$notification = null;

// Liste des avatars disponibles
$avatarsDir = __DIR__ . '/../public/images/profile/avatars/';
$avatars = [];
if (is_dir($avatarsDir)) {
    foreach (glob($avatarsDir . '*.svg') as $avatarPath) {
        $avatars[] = basename($avatarPath);
    }
}

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

    // Gestion de l'avatar et photo de profil
    $avatar = $_POST['avatar'] ?? $etudiant->getAvatar();
    $useCustomPhoto = isset($_POST['use_custom_photo']) ? true : false;

    // Traitement de l'upload de photo si demandé
    $customPhotoPath = null;
    if ($useCustomPhoto && isset($_FILES['custom_photo']) && $_FILES['custom_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../public/images/profile/uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['custom_photo']['name']);
        $uploadFile = $uploadDir . $fileName;

        // Vérification du type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $_FILES['custom_photo']['tmp_name']);
        finfo_close($fileInfo);

        if (in_array($mimeType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['custom_photo']['tmp_name'], $uploadFile)) {
                $customPhotoPath = $fileName;
                // Si l'utilisateur télécharge une photo personnalisée, on désactive l'avatar prédéfini
                $avatar = null;
            } else {
                $notification = ['message' => 'Erreur lors du téléchargement de l\'image.', 'type' => 'error'];
            }
        } else {
            $notification = ['message' => 'Le fichier doit être une image (JPG, PNG ou GIF).', 'type' => 'error'];
        }
    }

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
                $stmtUpdate = $pdo->prepare('UPDATE etudiant SET nom = :nom, promotion = :promotion, telephone = :telephone, password = :password, avatar = :avatar, photo_profile = :photo_profile WHERE id = :id');
                $stmtUpdate->execute([
                    ':nom' => $nom,
                    ':promotion' => $promotion,
                    ':telephone' => $telephone,
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                    ':avatar' => $avatar,
                    ':photo_profile' => $customPhotoPath,
                    ':id' => $id
                ]);
            } else {
                // Mise à jour sans changer le mot de passe
                $stmtUpdate = $pdo->prepare('UPDATE etudiant SET nom = :nom, promotion = :promotion, telephone = :telephone, avatar = :avatar, photo_profile = :photo_profile WHERE id = :id');
                $stmtUpdate->execute([
                    ':nom' => $nom,
                    ':promotion' => $promotion,
                    ':telephone' => $telephone,
                    ':avatar' => $avatar,
                    ':photo_profile' => $customPhotoPath,
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
$profileImg = '/public/public/images/profile/' . $etudiant->getId() . '.png';
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
                    <!-- En-tête avec photo de profil et avatar actuel -->
                    <div class="flex flex-col items-center mb-8">
                        <div class="relative mb-4 group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-700 rounded-full opacity-75 blur-sm group-hover:opacity-100 transition duration-500"></div>
                            <div class="relative w-24 h-24 rounded-full overflow-hidden shadow-lg">
                                <?php if ($etudiant->getPhotoProfile()): ?>
                                    <img src="/public/images/profile/uploads/<?= htmlspecialchars($etudiant->getPhotoProfile()) ?>" alt="Photo de profil" class="w-full h-full object-cover">
                                <?php elseif ($etudiant->getAvatar()): ?>
                                    <img src="/public/images/profile/avatars/<?= htmlspecialchars($etudiant->getAvatar()) ?>" alt="Avatar" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <img src="/public/images/profile/avatars/default.svg" alt="Avatar par défaut" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($etudiant->getNom()) ?></h2>
                        <p class="text-gray-600 dark:text-gray-400">ID: <?= htmlspecialchars($etudiant->getId()) ?></p>
                    </div>

                    <form action="/edit_profile" method="post" class="space-y-6" enctype="multipart/form-data">
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

                        <!-- Sélection d'avatar -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Changer d'image de profil</h3>

                            <div class="mb-6">
                                <div class="flex items-center mb-4">
                                    <input id="use-avatar" type="radio" name="profile_type" value="avatar"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        <?php echo (!$etudiant->getPhotoProfile()) ? 'checked' : ''; ?>>
                                    <label for="use-avatar" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Utiliser un avatar prédéfini
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="use-custom-photo" type="radio" name="profile_type" value="custom"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        <?php echo ($etudiant->getPhotoProfile()) ? 'checked' : ''; ?>>
                                    <label for="use-custom-photo" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Télécharger ma propre photo
                                    </label>
                                </div>
                            </div>

                            <!-- Section avatars prédéfinis -->
                            <div id="avatars-section" class="<?php echo ($etudiant->getPhotoProfile()) ? 'hidden' : ''; ?> avatar-selection-section">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-4 flex-1">
                                        <?php foreach ($avatars as $avatar): ?>
                                            <div class="avatar-option">
                                                <input type="radio" name="avatar" id="avatar-<?php echo $avatar; ?>"
                                                    value="<?php echo $avatar; ?>" <?php echo ($avatar === $etudiant->getAvatar() || ($avatar === 'default.svg' && !$etudiant->getAvatar() && !$etudiant->getPhotoProfile())) ? 'checked' : ''; ?>
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
                                            <img
                                                id="avatar-preview"
                                                src="/public/images/profile/avatars/<?= htmlspecialchars($etudiant->getAvatar() ?: 'default.svg', ENT_QUOTES) ?>"
                                                alt="Avatar prévisualisé"
                                                class="w-full h-full rounded-full object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section téléchargement de photo -->
                            <div id="custom-photo-section" class="<?php echo (!$etudiant->getPhotoProfile()) ? 'hidden' : ''; ?> photo-upload-section">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="custom_photo">
                                        Télécharger votre photo
                                    </label>
                                    <div class="flex items-center justify-center w-full">
                                        <label for="custom_photo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Cliquez pour télécharger</span></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG ou GIF</p>
                                            </div>
                                            <input id="custom_photo" name="custom_photo" type="file" class="hidden" accept="image/png, image/jpeg, image/gif" />
                                            <input type="hidden" name="use_custom_photo" id="use_custom_photo" value="<?php echo ($etudiant->getPhotoProfile()) ? '1' : '0'; ?>" />
                                        </label>
                                    </div>
                                </div>
                                <!-- Conteneur de prévisualisation toujours présent -->
                                <div class="flex flex-col items-center mt-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Prévisualisation</div>
                                    <div class="w-32 h-32 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                        <img src="<?php echo $etudiant->getPhotoProfile() ? '/public/images/profile/uploads/' . htmlspecialchars($etudiant->getPhotoProfile()) : ''; ?>" alt="Prévisualisation de l'image" class="w-full h-full object-cover" id="current-photo-preview">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Toggle optionnel pour modification du mot de passe -->
                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center mb-4">
                                <input type="checkbox" id="toggle-password" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <label for="toggle-password" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Modifier le mot de passe (optionnel)</label>
                            </div>
                            <div id="password-section" class="space-y-4 hidden">
                                <!-- Section mot de passe -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nouveau mot de passe</label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="••••••••" minlength="8">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Laissez vide pour conserver votre mot de passe actuel</p>
                                </div>
                                <div>
                                    <label for="password_confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirmer le nouveau mot de passe</label>
                                    <div class="relative">
                                        <input type="password" id="password_confirm" name="password_confirm" class="pl-10 w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="••••••••" minlength="8">
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

<script src="/public/js/avatar-selector.js"></script>
<script src="/public/js/photo-profile-manager.js"></script>