<?php
namespace App\Model;
use \PDO;
use \PDOException;
class Connection {
    private $host = 'localhost';
    private $db_name = 'tp';  // Base de données utilisée par le projet
    private $username = 'root';  // Valeur commune trouvée dans d'autres fichiers
    private $password = 'root';  // Valeur commune trouvée dans d'autres fichiers
    private $conn;
    
    private static $instance = null;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
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