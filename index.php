<?php
/**
 * Fichier d'entrée principal qui redirige vers public/index.php
 * Ce fichier permet de garder public/ comme racine web tout en permettant
 * d'accéder au site directement par la racine du projet.
 */

// Charger l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Charger la configuration centralisée
require_once __DIR__ . '/config.php';

// Rediriger toutes les requêtes vers le point d'entrée public/index.php
require_once __DIR__ . '/public/index.php';
