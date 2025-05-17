<?php
namespace App\Model;
use \PDO;
use \PDOException;

class Connection {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    
    private static $instance = null;

    public function __construct() {
        // Charger les variables d'environnement si elles ne sont pas déjà chargées
        $this->loadEnvironmentVariables();
        
        // Récupérer les valeurs depuis les variables d'environnement
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'tp';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? 'root';
        
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]);
        } catch (PDOException $exception) {
            if ($_ENV['APP_ENV'] === 'development') {
                echo "Connection error: " . $exception->getMessage();
            } else {
                // En production, ne pas afficher les détails de l'erreur
                echo "Une erreur est survenue lors de la connexion à la base de données.";
                error_log("Connection error: " . $exception->getMessage());
            }
        }
    }
    
    /**
     * Charge les variables d'environnement depuis le fichier .env
     */
    private function loadEnvironmentVariables() {
        $dotenvFile = __DIR__ . '/../../.env';
        
        if (file_exists($dotenvFile)) {
            if (class_exists('\\Dotenv\\Dotenv')) {
                $dotenv = \Dotenv\Dotenv::createImmutable(dirname($dotenvFile));
                $dotenv->safeLoad();
            }
        }
    }

    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Obtient une instance unique de Connection (pattern Singleton)
     * @return PDO Une instance PDO pour la connexion à la base de données
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->getConnection();
    }
}