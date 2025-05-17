<?php
namespace App\Util;

use App\Model\Connection;
use App\Util\Logger;
use PDO;

/**
 * Classe Database pour centraliser les opérations de base de données
 */
class Database {
    private static $instance = null;
    private $pdo;
    private $logger;
    
    /**
     * Constructeur privé (pattern Singleton)
     */
    private function __construct() {
        $this->pdo = Connection::getInstance();
        $this->logger = Logger::getInstance();
    }
    
    /**
     * Obtenir l'instance unique de Database
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Exécuter une requête et retourner tous les résultats
     * 
     * @param string $query Requête SQL
     * @param array $params Paramètres de la requête
     * @return array Résultats de la requête
     */
    public function fetchAll($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans fetchAll: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Exécuter une requête et retourner le premier résultat
     * 
     * @param string $query Requête SQL
     * @param array $params Paramètres de la requête
     * @return array|null Premier résultat ou null
     */
    public function fetchOne($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans fetchOne: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Exécuter une requête et retourner un tableau d'une seule colonne
     * 
     * @param string $query Requête SQL
     * @param array $params Paramètres de la requête
     * @param int $column Index de la colonne à retourner
     * @return array Liste des valeurs de la colonne
     */
    public function fetchColumn($query, $params = [], $column = 0) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_COLUMN, $column);
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans fetchColumn: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Exécuter une requête et retourner la première valeur d'une colonne
     * 
     * @param string $query Requête SQL
     * @param array $params Paramètres de la requête
     * @param int $column Index de la colonne à retourner
     * @return mixed Valeur ou null
     */
    public function fetchValue($query, $params = [], $column = 0) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchColumn($column);
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans fetchValue: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Exécuter une requête de modification (INSERT, UPDATE, DELETE)
     * 
     * @param string $query Requête SQL
     * @param array $params Paramètres de la requête
     * @return int|bool Nombre de lignes affectées ou false
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans execute: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Insérer des données dans une table
     * 
     * @param string $table Nom de la table
     * @param array $data Données à insérer (clé => valeur)
     * @return int|bool ID de la nouvelle ligne ou false
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array_values($data));
            
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans insert: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mettre à jour des données dans une table
     * 
     * @param string $table Nom de la table
     * @param array $data Données à mettre à jour (clé => valeur)
     * @param string $where Condition WHERE
     * @param array $params Paramètres pour la condition WHERE
     * @return int|bool Nombre de lignes affectées ou false
     */
    public function update($table, $data, $where, $params = []) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "$column = ?";
        }
        
        $query = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array_merge(array_values($data), $params));
            
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans update: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer des lignes d'une table
     * 
     * @param string $table Nom de la table
     * @param string $where Condition WHERE
     * @param array $params Paramètres pour la condition WHERE
     * @return int|bool Nombre de lignes affectées ou false
     */
    public function delete($table, $where, $params = []) {
        $query = "DELETE FROM $table WHERE $where";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            $this->logger->error("Erreur dans delete: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compter le nombre de lignes dans une table
     * 
     * @param string $table Nom de la table
     * @param string $where Condition WHERE (optionnelle)
     * @param array $params Paramètres pour la condition WHERE
     * @return int|null Nombre de lignes ou null
     */
    public function count($table, $where = '1=1', $params = []) {
        $query = "SELECT COUNT(*) FROM $table WHERE $where";
        
        return $this->fetchValue($query, $params);
    }
    
    /**
     * Démarrer une transaction
     * 
     * @return bool Succès ou échec
     */
    public function beginTransaction() {
        try {
            return $this->pdo->beginTransaction();
        } catch (\PDOException $e) {
            $this->logger->error("Erreur lors du démarrage d'une transaction: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Valider une transaction
     * 
     * @return bool Succès ou échec
     */
    public function commit() {
        try {
            return $this->pdo->commit();
        } catch (\PDOException $e) {
            $this->logger->error("Erreur lors de la validation d'une transaction: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Annuler une transaction
     * 
     * @return bool Succès ou échec
     */
    public function rollBack() {
        try {
            return $this->pdo->rollBack();
        } catch (\PDOException $e) {
            $this->logger->error("Erreur lors de l'annulation d'une transaction: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtenir l'objet PDO sous-jacent
     * 
     * @return PDO
     */
    public function getPdo() {
        return $this->pdo;
    }
}
