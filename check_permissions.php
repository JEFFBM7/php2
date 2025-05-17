<?php
/**
 * Script pour vérifier et réparer les permissions des dossiers
 * 
 * Utilisation : php check_permissions.php [--fix]
 * L'option --fix tentera de corriger les permissions incorrectes
 */

// Charger la configuration
require_once __DIR__ . '/config.php';

$fix = isset($argv[1]) && $argv[1] === '--fix';

echo "Vérification des permissions des dossiers...\n";

// Liste des dossiers qui doivent être accessibles en écriture
$writableFolders = [
    UPLOADS_PATH,
    PROFILE_PATH,
    PUBLIC_PATH . '/images/profile/avatars',
    PUBLIC_PATH . '/images/produits',
];

// Vérifier et éventuellement corriger les permissions
$allOk = true;
foreach ($writableFolders as $folder) {
    echo "Vérification de $folder: ";
    
    // Créer le dossier s'il n'existe pas
    if (!file_exists($folder)) {
        echo "n'existe pas. ";
        if ($fix) {
            if (mkdir($folder, 0755, true)) {
                echo "Dossier créé avec succès.\n";
            } else {
                echo "ERREUR: Impossible de créer le dossier!\n";
                $allOk = false;
            }
        } else {
            echo "Utilisez --fix pour créer le dossier.\n";
            $allOk = false;
        }
        continue;
    }
    
    // Vérifier si le dossier est accessible en écriture
    if (!is_writable($folder)) {
        echo "pas accessible en écriture. ";
        if ($fix) {
            if (chmod($folder, 0755)) {
                echo "Permissions corrigées.\n";
            } else {
                echo "ERREUR: Impossible de modifier les permissions!\n";
                $allOk = false;
            }
        } else {
            echo "Utilisez --fix pour corriger les permissions.\n";
            $allOk = false;
        }
    } else {
        echo "OK\n";
    }
}

// Résumé
if ($allOk) {
    echo "\nToutes les permissions sont correctes.\n";
} else {
    if ($fix) {
        echo "\nCertaines erreurs n'ont pas pu être corrigées automatiquement.\n";
        echo "Vérifiez les messages ci-dessus et réglez les problèmes manuellement.\n";
    } else {
        echo "\nProblèmes détectés. Exécutez avec --fix pour tenter de les corriger automatiquement.\n";
    }
}
