<?php
namespace App\Util;

/**
 * Classe Helper pour les fonctions utilitaires
 */
class Helper {
    /**
     * Formater un prix avec le symbole de devise
     * 
     * @param float $price Prix à formater
     * @param string $currency Code de la devise (USD, EUR, etc.)
     * @return string Prix formaté
     */
    public static function formatPrice($price, $currency = 'EUR') {
        $symbols = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'JPY' => '¥',
            'CHF' => 'Fr.'
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        
        // Formatage du nombre avec 2 décimales et séparateur de milliers
        $formattedPrice = number_format($price, 2, ',', ' ');
        
        if ($currency === 'EUR') {
            return $formattedPrice . ' ' . $symbol;
        } else {
            return $symbol . ' ' . $formattedPrice;
        }
    }
    
    /**
     * Tronque un texte à une longueur donnée
     * 
     * @param string $text Texte à tronquer
     * @param int $length Longueur maximale
     * @param string $append Texte à ajouter à la fin si tronqué
     * @return string Texte tronqué
     */
    public static function truncate($text, $length = 100, $append = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $append;
    }
    
    /**
     * Générer une URL conviviale (slug) à partir d'un texte
     * 
     * @param string $text Texte à convertir en slug
     * @return string Slug généré
     */
    public static function slugify($text) {
        // Remplacer les caractères accentués par leur équivalent non accentué
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $text);
        
        // Convertir en minuscules
        $text = strtolower($text);
        
        // Remplacer les caractères non alphanumériques par des tirets
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        
        // Supprimer les tirets en début et fin de chaîne
        $text = trim($text, '-');
        
        return $text;
    }
    
    /**
     * Générer un nom de fichier unique pour les uploads
     * 
     * @param string $extension Extension du fichier (sans le point)
     * @return string Nom de fichier unique
     */
    public static function generateUniqueFilename($extension) {
        return uniqid() . '.' . $extension;
    }
    
    /**
     * Déterminer le type MIME d'un fichier
     * 
     * @param string $file Chemin du fichier
     * @return string|false Type MIME ou false en cas d'échec
     */
    public static function getMimeType($file) {
        if (!file_exists($file)) {
            return false;
        }
        
        if (function_exists('mime_content_type')) {
            return mime_content_type($file);
        }
        
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $mime;
        }
        
        // Méthode de secours basée sur l'extension
        $extensions = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'zip' => 'application/zip'
        ];
        
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return $extensions[$ext] ?? 'application/octet-stream';
    }
    
    /**
     * Obtenir l'extension d'un fichier à partir de son type MIME
     * 
     * @param string $mimeType Type MIME
     * @return string Extension de fichier (sans le point)
     */
    public static function getExtensionFromMimeType($mimeType) {
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'text/plain' => 'txt',
            'text/html' => 'html',
            'application/zip' => 'zip'
        ];
        
        return $mimeToExt[$mimeType] ?? 'bin';
    }
    
    /**
     * Convertir une date en format lisible
     * 
     * @param string $date Date à formater (format Y-m-d ou datetime)
     * @param string $format Format de sortie
     * @return string Date formatée
     */
    public static function formatDate($date, $format = 'd/m/Y') {
        if (empty($date)) {
            return '';
        }
        
        $datetime = new \DateTime($date);
        return $datetime->format($format);
    }
    
    /**
     * Créer un tableau de pagination
     * 
     * @param int $totalItems Nombre total d'éléments
     * @param int $itemsPerPage Nombre d'éléments par page
     * @param int $currentPage Page actuelle
     * @param int $maxPages Nombre maximum de pages à afficher
     * @return array Informations de pagination
     */
    public static function paginate($totalItems, $itemsPerPage = 10, $currentPage = 1, $maxPages = 5) {
        $totalPages = ceil($totalItems / $itemsPerPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        
        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // Calculer les pages à afficher
        $startPage = max(1, $currentPage - floor($maxPages / 2));
        $endPage = min($totalPages, $startPage + $maxPages - 1);
        
        // Ajuster startPage si nécessaire
        if ($endPage - $startPage + 1 < $maxPages) {
            $startPage = max(1, $endPage - $maxPages + 1);
        }
        
        return [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
            'offset' => $offset,
            'pages' => range($startPage, $endPage),
            'hasPrevPage' => $currentPage > 1,
            'hasNextPage' => $currentPage < $totalPages,
            'prevPage' => max(1, $currentPage - 1),
            'nextPage' => min($totalPages, $currentPage + 1)
        ];
    }
}
