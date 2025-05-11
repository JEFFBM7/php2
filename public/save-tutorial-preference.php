<?php
// Endpoint pour sauvegarder les préférences de tutoriel de l'utilisateur
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer les données de la requête
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['enabled'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre "enabled" manquant']);
    exit;
}

$tutorialEnabled = (bool)$data['enabled'];
$userId = $_SESSION['user_id'];

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Mettre à jour la préférence dans la base de données
    // Note: On suppose qu'il existe une table user_preferences ou un champ dans la table utilisateur
    // Si la table est "etudiant", on peut ajouter une colonne "tutorial_enabled" (boolean)
    
    // Vérifier si la colonne tutorial_enabled existe dans la table etudiant
    $stmt = $pdo->query("SHOW COLUMNS FROM etudiant LIKE 'tutorial_enabled'");
    $columnExists = $stmt->fetchColumn();
    
    if ($columnExists) {
        // La colonne existe, mettre à jour la préférence
        $stmt = $pdo->prepare('UPDATE etudiant SET tutorial_enabled = ? WHERE id = ?');
        $stmt->execute([$tutorialEnabled ? 1 : 0, $userId]);
    } else {
        // Essayer de mettre à jour une table user_preferences si elle existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'user_preferences'");
        if ($stmt->rowCount() > 0) {
            // Vérifier si une entrée existe déjà pour cet utilisateur
            $stmt = $pdo->prepare('SELECT id FROM user_preferences WHERE user_id = ?');
            $stmt->execute([$userId]);
            
            if ($stmt->rowCount() > 0) {
                // Mettre à jour l'entrée existante
                $stmt = $pdo->prepare('UPDATE user_preferences SET tutorial_enabled = ? WHERE user_id = ?');
                $stmt->execute([$tutorialEnabled ? 1 : 0, $userId]);
            } else {
                // Créer une nouvelle entrée
                $stmt = $pdo->prepare('INSERT INTO user_preferences (user_id, tutorial_enabled) VALUES (?, ?)');
                $stmt->execute([$userId, $tutorialEnabled ? 1 : 0]);
            }
        } else {
            // Aucune table appropriée trouvée
            http_response_code(500);
            echo json_encode(['error' => 'Structure de base de données non supportée']);
            exit;
        }
    }
    
    // Réponse de succès
    echo json_encode(['success' => true, 'message' => 'Préférence de tutoriel mise à jour']);
} catch (Exception $e) {
    // En cas d'erreur, renvoyer un message d'erreur
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
