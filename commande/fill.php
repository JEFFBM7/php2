<?php
require_once __DIR__ . '/../vendor/autoload.php';
$faker = Faker\Factory::create('fr_FR');
$pdo = new PDO('mysql:host=localhost;dbname=tp', 'root', 'root',[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');
$pdo->exec('TRUNCATE TABLE etudiant_produit');
$pdo->exec('TRUNCATE TABLE etudiant');
$pdo->exec('TRUNCATE TABLE produit');
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

$etudiant_id_list = array();
$produits_id_list = array();


for ($i = 0; $i < 5; $i++) {
    $etudiant = $pdo->prepare("INSERT INTO etudiant (nom, promotion, telephone, password) VALUES (:nom, :promotion, :telephone, :password)");
    $etudiant->execute([
        ':nom' => $faker->firstName(),
        ':promotion' => 'l3',
        ':telephone' => $faker->e164PhoneNumber(),
        ':password' => '123',
    ]);
    $etudiant_id_list[] = $pdo->lastInsertId();
}

for ($i = 0; $i < 10; $i++) {
$produit = $pdo->prepare("INSERT INTO produit (etudiant_id, nom, description, image, prix,devis) VALUES (:etudiant_id, :nom, :description, :image, :prix, :devis)");
    $produit->execute([
        ':etudiant_id' => $faker->randomElement($etudiant_id_list),
        ':nom' => $faker->word(),
        ':description' => $faker->sentence(),
        ':image' => $faker->imageUrl(640, 480),
        ':prix' => $faker->randomFloat(2, 1, 100),
        ':devis' => 'USD',
    ]);
    $produits_id_list[] = $pdo->lastInsertId();
}

foreach ($etudiant_id_list as $etudiant_id) {
    // on crée de 1 à N associations produits pour chaque étudiant
    $randomAssociations = $faker->randomElements(
        $produits_id_list, 
        rand(1, count($produits_id_list))
    );

    foreach ($randomAssociations as $produit_id) {
        $stmt = $pdo->prepare(
            "INSERT INTO etudiant_produit (etudiant_id, produit_id) 
             VALUES (:etudiant_id, :produit_id)"
        );
        $stmt->execute([
            ':etudiant_id' => $etudiant_id,
            ':produit_id'  => $produit_id,
        ]);
    }
}