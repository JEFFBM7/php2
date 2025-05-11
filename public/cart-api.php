<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Panier;

header('Content-Type: application/json');

// S'assurer que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;
    
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de produit invalide']);
        exit;
    }
    
    if (Panier::ajouter($productId, $quantite)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Produit ajouté au panier',
            'cartCount' => Panier::getNombreArticles()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout au panier']);
    }
    exit;
} elseif ($action === 'remove') {
    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
    
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de produit invalide']);
        exit;
    }
    
    if (Panier::supprimer($productId)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Produit retiré du panier',
            'cartCount' => Panier::getNombreArticles()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du panier']);
    }
    exit;
} elseif ($action === 'update') {
    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
    $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;
    
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de produit invalide']);
        exit;
    }
    
    if (Panier::mettreAJourQuantite($productId, $quantite)) {
        echo json_encode([
            'success' => true,
            'message' => 'Quantité mise à jour',
            'cartCount' => Panier::getNombreArticles()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la quantité']);
    }
    exit;
} elseif ($action === 'clear') {
    Panier::vider();
    echo json_encode([
        'success' => true,
        'message' => 'Panier vidé',
        'cartCount' => 0
    ]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    exit;
}