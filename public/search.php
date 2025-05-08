<?php
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

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
