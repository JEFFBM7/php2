<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Model\Connection;

header('Content-Type: application/json');

$pdo = Connection::getInstance();

$searchTerm = $_GET['search'] ?? '';

$stmt = $pdo->prepare(
    'SELECT p.*, e.nom AS etudiant_nom FROM produit p
     LEFT JOIN etudiant e ON p.etudiant_id = e.id
     WHERE p.nom LIKE :search
     ORDER BY p.id DESC LIMIT 20'
);
$stmt->execute(['search' => "%$searchTerm%"]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($produits);
