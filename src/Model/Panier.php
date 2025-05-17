<?php
namespace App\Model;

/**
 * Class Panier
 * @package App\Model
 * 
 * Représente un panier d'achat contenant des produits.
 */
class Panier {
    /**
     * Ajoute un produit au panier.
     *
     * @param int $produit_id L'ID du produit à ajouter
     * @param int $quantite La quantité à ajouter (par défaut: 1)
     * @return bool True si le produit a été ajouté avec succès, false sinon
     */
    public static function ajouter($produit_id, $quantite = 1) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialiser le panier s'il n'existe pas encore
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        
        // Vérifier si le produit existe
        try {
            $pdo = Connection::getInstance();
            
            $stmt = $pdo->prepare('SELECT * FROM produit WHERE id = :id');
            $stmt->execute(['id' => $produit_id]);
            $produit = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$produit) {
                return false; // Produit non trouvé
            }
            
            // Ajouter ou mettre à jour le produit dans le panier
            if (isset($_SESSION['panier'][$produit_id])) {
                $_SESSION['panier'][$produit_id]['quantite'] += $quantite;
            } else {
                $_SESSION['panier'][$produit_id] = [
                    'id' => $produit_id,
                    'nom' => $produit['nom'],
                    'prix' => $produit['prix'],
                    'devis' => $produit['devis'],
                    'image' => $produit['image'],
                    'quantite' => $quantite
                ];
            }
            
            return true;
        } catch (\PDOException $e) {
            // En cas d'erreur, retourner false
            return false;
        }
    }
    
    /**
     * Supprime un produit du panier.
     *
     * @param int $produit_id L'ID du produit à supprimer
     * @return bool True si le produit a été supprimé avec succès, false sinon
     */
    public static function supprimer($produit_id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['panier'][$produit_id])) {
            unset($_SESSION['panier'][$produit_id]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Met à jour la quantité d'un produit dans le panier.
     *
     * @param int $produit_id L'ID du produit
     * @param int $quantite La nouvelle quantité
     * @return bool True si la quantité a été mise à jour, false sinon
     */
    public static function mettreAJourQuantite($produit_id, $quantite) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['panier'][$produit_id])) {
            if ($quantite <= 0) {
                return self::supprimer($produit_id);
            } else {
                $_SESSION['panier'][$produit_id]['quantite'] = $quantite;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vide complètement le panier.
     *
     * @return void
     */
    public static function vider() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['panier'] = [];
    }
    
    /**
     * Récupère le contenu du panier.
     *
     * @return array Le contenu du panier
     */
    public static function getContenu() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['panier']) ? $_SESSION['panier'] : [];
    }
    
    /**
     * Calcule le total du panier.
     *
     * @return array Tableau avec le montant total et la devise
     */
    public static function getTotal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $total = 0;
        $devis = 'EUR'; // Devise par défaut
        
        if (isset($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $item) {
                $total += $item['prix'] * $item['quantite'];
                // On prend la devise du dernier article (on suppose que tous les articles ont la même devise)
                $devis = $item['devis'] ?: 'EUR';
            }
        }
        
        return [
            'montant' => $total,
            'devis' => $devis
        ];
    }
    
    /**
     * Récupère le nombre d'articles dans le panier.
     *
     * @return int Le nombre d'articles
     */
    public static function getNombreArticles() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $nombre = 0;
        
        if (isset($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $item) {
                $nombre += $item['quantite'];
            }
        }
        
        return $nombre;
    }
}