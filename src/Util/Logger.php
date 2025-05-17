<?php
namespace App\Util;

/**
 * Classe Logger pour gérer les logs de l'application
 */
class Logger {
    private static $instance = null;
    private $logFile;
    private $logEnabled;
    
    /**
     * Constructeur privé (pattern Singleton)
     */
    private function __construct() {
        $this->logEnabled = defined('DEBUG_MODE') ? DEBUG_MODE : false;
        $this->logFile = defined('APP_ROOT') ? APP_ROOT . '/logs/app.log' : __DIR__ . '/../../logs/app.log';
        
        // Créer le dossier de logs s'il n'existe pas
        $logDir = dirname($this->logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Obtenir l'instance unique du logger
     * 
     * @return Logger
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Écriture d'un message dans le log
     * 
     * @param string $message Message à logger
     * @param string $level Niveau de log (INFO, WARNING, ERROR, DEBUG)
     * @return void
     */
    public function log($message, $level = 'INFO') {
        if (!$this->logEnabled && $level !== 'ERROR') {
            return;
        }
        
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] [$level] $message" . PHP_EOL;
        
        error_log($logMessage, 3, $this->logFile);
    }
    
    /**
     * Log un message de niveau INFO
     * 
     * @param string $message Message à logger
     */
    public function info($message) {
        $this->log($message, 'INFO');
    }
    
    /**
     * Log un message de niveau WARNING
     * 
     * @param string $message Message à logger
     */
    public function warning($message) {
        $this->log($message, 'WARNING');
    }
    
    /**
     * Log un message de niveau ERROR
     * 
     * @param string $message Message à logger
     */
    public function error($message) {
        $this->log($message, 'ERROR');
    }
    
    /**
     * Log un message de niveau DEBUG
     * 
     * @param string $message Message à logger
     */
    public function debug($message) {
        $this->log($message, 'DEBUG');
    }
}
