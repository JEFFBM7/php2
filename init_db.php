<?php
/**
 * Script d'initialisation de la base de données
 * 
 * Exécuter ce script une fois pour initialiser la base de données
 * Usage: php init_db.php [--force]
 * L'option --force permet d'écraser une base de données existante
 */

// Charger la configuration
require_once __DIR__ . '/config.php';

// Vérifier si l'option --force est présente
$force = isset($argv[1]) && $argv[1] === '--force';

// Connexion à MySQL sans spécifier de base de données
try {
    $dsn = "mysql:host=" . DB_HOST;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    echo "Connexion à MySQL réussie.\n";
} catch (PDOException $e) {
    die("Erreur de connexion à MySQL: " . $e->getMessage() . "\n");
}

// Vérifier si la base de données existe déjà
$stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
$stmt->execute([DB_NAME]);
$dbExists = $stmt->fetch(PDO::FETCH_ASSOC);

if ($dbExists && !$force) {
    die("La base de données '" . DB_NAME . "' existe déjà. Utilisez --force pour la recréer.\n");
}

// Supprimer la base de données si elle existe et que l'option --force est utilisée
if ($dbExists && $force) {
    echo "Suppression de la base de données existante...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `" . DB_NAME . "`");
}

// Créer la base de données
try {
    echo "Création de la base de données '" . DB_NAME . "'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de données créée avec succès.\n";
} catch (PDOException $e) {
    die("Erreur lors de la création de la base de données: " . $e->getMessage() . "\n");
}

// Sélectionner la base de données
$pdo->exec("USE `" . DB_NAME . "`");

// Charger et exécuter le schéma SQL
echo "Création des tables...\n";

// Liste des fichiers SQL à exécuter dans l'ordre
$sqlFiles = [
    __DIR__ . '/db.sql',
    __DIR__ . '/create_tables.sql',
    __DIR__ . '/create_tutorial_table.sql',
    __DIR__ . '/update_avatar_field.sql',
    __DIR__ . '/update_photo_profile_field.sql',
    __DIR__ . '/update_tutorial_preferences.sql'
];

foreach ($sqlFiles as $sqlFile) {
    if (file_exists($sqlFile)) {
        echo "Exécution de " . basename($sqlFile) . "...\n";
        $sql = file_get_contents($sqlFile);
        
        // Diviser le fichier SQL en instructions individuelles
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        try {
            foreach ($queries as $query) {
                if (!empty($query)) {
                    $pdo->exec($query);
                }
            }
            echo "Fichier " . basename($sqlFile) . " exécuté avec succès.\n";
        } catch (PDOException $e) {
            echo "Erreur lors de l'exécution de " . basename($sqlFile) . ": " . $e->getMessage() . "\n";
            echo "Requête en échec: " . substr($query, 0, 100) . "...\n";
        }
    } else {
        echo "Avertissement: Le fichier " . basename($sqlFile) . " n'existe pas.\n";
    }
}

// Créer un compte administrateur si la table etudiant existe
try {
    $stmt = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = 'etudiant'");
    $stmt->execute([DB_NAME]);
    
    if ($stmt->fetch()) {
        echo "Création d'un compte administrateur...\n";
        
        // Vérifier si l'administrateur existe déjà
        $stmt = $pdo->prepare("SELECT id FROM etudiant WHERE nom = 'admin'");
        $stmt->execute();
        
        if ($stmt->fetch()) {
            echo "Le compte administrateur existe déjà.\n";
        } else {
            // Hacher le mot de passe
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            
            // Insérer l'administrateur
            $stmt = $pdo->prepare("INSERT INTO etudiant (nom, promotion, telephone, password) VALUES (?, ?, ?, ?)");
            $stmt->execute(['admin', 'Administration', '0123456789', $hashedPassword]);
            
            echo "Compte administrateur créé avec succès.\n";
            echo "Identifiant: admin\n";
            echo "Mot de passe: admin123\n";
        }
    }
} catch (PDOException $e) {
    echo "Erreur lors de la création du compte administrateur: " . $e->getMessage() . "\n";
}

echo "\n--------------------------------------\n";
echo "Initialisation de la base de données terminée.\n";
echo "Base de données: " . DB_NAME . "\n";
echo "Hôte: " . DB_HOST . "\n";
echo "Utilisateur: " . DB_USER . "\n";
echo "\nModifiez le fichier .env pour personnaliser la configuration.\n";
echo "--------------------------------------\n";
